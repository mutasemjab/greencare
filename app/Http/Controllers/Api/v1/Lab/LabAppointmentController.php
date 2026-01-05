<?php

namespace App\Http\Controllers\Api\v1\Lab;

use App\Http\Controllers\Controller;
use App\Models\MedicalTest;
use App\Models\HomeXray;
use App\Models\AppointmentResult;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class LabAppointmentController extends Controller
{
    use Responses;

    /**
     * Get all appointments for the authenticated lab
     */
    public function getAppointments(Request $request)
    {
        try {
            $lab = Auth::guard('lab-api')->user();

            $validator = Validator::make($request->all(), [
                'type' => 'nullable|in:medical_test,home_xray,all',
                'status' => 'nullable|in:pending,confirmed,processing,finished,cancelled',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            $type = $request->get('type', 'all');
            $status = $request->get('status');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            $appointments = collect();

            // Get Medical Tests
            if ($type === 'all' || $type === 'medical_test') {
                $medicalTests = MedicalTest::with(['user', 'typeMedicalTest', 'room', 'result'])
                    ->where('lab_id', $lab->id)
                    ->when($status, function ($query) use ($status) {
                        return $query->where('status', $status);
                    })
                    ->when($dateFrom, function ($query) use ($dateFrom) {
                        return $query->whereDate('date_of_appointment', '>=', $dateFrom);
                    })
                    ->when($dateTo, function ($query) use ($dateTo) {
                        return $query->whereDate('date_of_appointment', '<=', $dateTo);
                    })
                    ->orderBy('date_of_appointment', 'desc')
                    ->orderBy('time_of_appointment', 'desc')
                    ->get()
                    ->map(function ($item) {
                        return $this->formatAppointment($item, 'medical_test');
                    });

                $appointments = $appointments->merge($medicalTests);
            }

            // Get Home X-rays
            if ($type === 'all' || $type === 'home_xray') {
                $homeXrays = HomeXray::with(['user', 'typeHomeXray', 'room', 'result'])
                    ->where('lab_id', $lab->id)
                    ->when($status, function ($query) use ($status) {
                        return $query->where('status', $status);
                    })
                    ->when($dateFrom, function ($query) use ($dateFrom) {
                        return $query->whereDate('date_of_appointment', '>=', $dateFrom);
                    })
                    ->when($dateTo, function ($query) use ($dateTo) {
                        return $query->whereDate('date_of_appointment', '<=', $dateTo);
                    })
                    ->orderBy('date_of_appointment', 'desc')
                    ->orderBy('time_of_appointment', 'desc')
                    ->get()
                    ->map(function ($item) {
                        return $this->formatAppointment($item, 'home_xray');
                    });

                $appointments = $appointments->merge($homeXrays);
            }

            // Sort by date if showing all types
            if ($type === 'all') {
                $appointments = $appointments->sortByDesc('date_of_appointment')->values();
            }

            return $this->success_response(
                __('messages.appointments_fetched_successfully'),
                [
                    'appointments' => $appointments,
                    'total' => $appointments->count()
                ]
            );
        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_occurred'),
                ['error' => $e->getMessage()]
            );
        }
    }


    /**
     * Update appointment status
     */
    public function updateAppointmentStatus(Request $request)
    {
        try {
            $lab = Auth::guard('lab-api')->user();

            $validator = Validator::make($request->all(), [
                'appointment_type' => 'required|in:medical_test,home_xray',
                'appointment_id' => 'required|integer',
                'status' => 'required|in:confirmed,processing,finished,cancelled',
                'cancellation_reason' => 'required_if:status,cancelled|string|max:500',
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            $appointment = $this->getAppointmentModel($request->appointment_type, $request->appointment_id);

            if (!$appointment) {
                return $this->error_response(__('messages.appointment_not_found'), []);
            }

            if ($appointment->lab_id != $lab->id) {
                return $this->error_response(__('messages.unauthorized_action'), []);
            }

            $updateData = ['status' => $request->status];
            
            if ($request->status === 'cancelled' && $request->cancellation_reason) {
                $updateData['note'] = ($appointment->note ? $appointment->note . "\n\n" : '') . 
                    "Cancellation Reason: " . $request->cancellation_reason;
            }

            $appointment->update($updateData);
            $appointment->load(['user', 'room']);

            return $this->success_response(
                __('messages.status_updated_successfully'),
                ['appointment' => $this->formatAppointment($appointment, $request->appointment_type)]
            );
        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_occurred'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Upload appointment results
     */
    public function uploadResults(Request $request)
    {
        try {
            $lab = Auth::guard('lab-api')->user();

            $validator = Validator::make($request->all(), [
                'appointment_type' => 'required|in:medical_test,home_xray',
                'appointment_id' => 'required|integer',
                'notes' => 'nullable|string|max:2000',
                'files' => 'required|array|min:1|max:10',
                'files.*' => 'file|mimes:pdf,jpg,jpeg,png', // 10MB max per file
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            $appointment = $this->getAppointmentModel($request->appointment_type, $request->appointment_id);

            if (!$appointment) {
                return $this->error_response(__('messages.appointment_not_found'), []);
            }

            if ($appointment->lab_id != $lab->id) {
                return $this->error_response(__('messages.unauthorized_action'), []);
            }

            // Upload files
            $uploadedFiles = [];
            foreach ($request->file('files') as $file) {
                $path = uploadImage('assets/admin/uploads', $file);
                $uploadedFiles[] = $path;
            }

            // Create or update result
            $result = $appointment->result;
            
            if ($result) {
                // Append new files to existing ones
                $existingFiles = $result->files ?? [];
                $result->update([
                    'files' => array_merge($existingFiles, $uploadedFiles),
                    'notes' => $request->notes ?? $result->notes,
                    'completed_at' => now(),
                ]);
            } else {
                $result = AppointmentResult::create([
                    'appointment_type' => $request->appointment_type === 'medical_test' ? MedicalTest::class : HomeXray::class,
                    'appointment_id' => $appointment->id,
                    'lab_id' => $lab->id,
                    'files' => $uploadedFiles,
                    'notes' => $request->notes,
                    'completed_at' => now(),
                ]);
            }

            // Update appointment status to finished
            $appointment->update(['status' => 'finished']);

            return $this->success_response(
                __('messages.results_uploaded_successfully'),
                [
                    'result' => [
                        'id' => $result->id,
                        'files' => $result->file_urls,
                        'notes' => $result->notes,
                        'completed_at' => $result->completed_at->format('Y-m-d H:i:s'),
                    ]
                ]
            );
        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_occurred'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Get appointment details
     */
    public function getAppointmentDetails(Request $request, $type, $id)
    {
        try {
            $lab = Auth::guard('lab-api')->user();

            if (!in_array($type, ['medical_test', 'home_xray'])) {
                return $this->error_response(__('messages.invalid_appointment_type'), []);
            }

            $appointment = $this->getAppointmentModel($type, $id);

            if (!$appointment) {
                return $this->error_response(__('messages.appointment_not_found'), []);
            }

            if ($appointment->lab_id != $lab->id) {
                return $this->error_response(__('messages.unauthorized_action'), []);
            }

            $appointment->load(['user', 'room', 'result']);

            return $this->success_response(
                __('messages.appointment_details_fetched'),
                ['appointment' => $this->formatAppointment($appointment, $type, true)]
            );
        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_occurred'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Helper: Get appointment model
     */
    private function getAppointmentModel($type, $id)
    {
        if ($type === 'medical_test') {
            return MedicalTest::find($id);
        } elseif ($type === 'home_xray') {
            return HomeXray::find($id);
        }
        return null;
    }

    /**
     * Helper: Format appointment data
     */
    private function formatAppointment($appointment, $type, $detailed = false)
    {
        $data = [
            'id' => $appointment->id,
            'type' => $type,
            'status' => $appointment->status,
            'status_name' => $appointment->status_name,
            'date_of_appointment' => $appointment->date_of_appointment->format('Y-m-d'),
            'time_of_appointment' => $appointment->time_of_appointment ? $appointment->time_of_appointment->format('H:i') : null,
            'address' => $appointment->address,
            'location' => [
                'lat' => $appointment->lat,
                'lng' => $appointment->lng,
            ],
            'note' => $appointment->note,
            'user' => [
                'id' => $appointment->user->id,
                'name' => $appointment->user->name,
                'phone' => $appointment->user->phone,
            ],
            'created_at' => $appointment->created_at->format('Y-m-d H:i:s'),
        ];

        // Add service information
        if ($type === 'medical_test') {
            $data['service'] = [
                'id' => $appointment->typeMedicalTest->id,
                'name' => $appointment->typeMedicalTest->name,
                'price' => $appointment->typeMedicalTest->price,
            ];
        } else {
            $data['service'] = [
                'id' => $appointment->typeHomeXray->id,
                'name' => $appointment->typeHomeXray->name,
                'price' => $appointment->typeHomeXray->price,
            ];
        }

        // Add room info if available
        if ($appointment->room) {
            $data['room'] = [
                'id' => $appointment->room->id,
                'code' => $appointment->room->code,
                'name' => $appointment->room->name ?? null,
            ];
        }

        // Add result info if available
        if ($appointment->result) {
            $data['result'] = [
                'id' => $appointment->result->id,
                'files' => $appointment->result->file_urls,
                'notes' => $appointment->result->notes,
                'completed_at' => $appointment->result->completed_at ? $appointment->result->completed_at->format('Y-m-d H:i:s') : null,
            ];
        }

        return $data;
    }
}