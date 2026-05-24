<?php

namespace App\Http\Controllers\Api\v1\Lab;

use App\Http\Controllers\Controller;
use App\Models\AppointmentResult;
use App\Models\HomeXray;
use App\Models\MedicalTest;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LabAppointmentController extends Controller
{
    use Responses;

    /**
     * List medical tests and/or home xrays assigned to the authenticated lab.
     * Query params:
     *   type   = medical_test|home_xray|all  (default: all)
     *   status = pending|confirmed|processing|finished|cancelled
     */
    public function index(Request $request)
    {
        $lab = Auth::guard('lab-api')->user();

        $type   = $request->get('type', 'all');
        $status = $request->get('status');

        $results = collect();

        if (in_array($type, ['medical_test', 'all'])) {
            $query = MedicalTest::with(['typeMedicalTest', 'user', 'room', 'result'])
                ->where('lab_id', $lab->id);

            if ($status) {
                $query->where('status', $status);
            }

            $results = $results->merge(
                $query->orderBy('date_of_appointment', 'desc')->get()
                    ->map(fn($item) => $this->formatMedicalTest($item))
            );
        }

        if (in_array($type, ['home_xray', 'all'])) {
            $query = HomeXray::with(['typeHomeXray.parent', 'user', 'room', 'result'])
                ->where('lab_id', $lab->id);

            if ($status) {
                $query->where('status', $status);
            }

            $results = $results->merge(
                $query->orderBy('date_of_appointment', 'desc')->get()
                    ->map(fn($item) => $this->formatHomeXray($item))
            );
        }

        return $this->success_response('Appointments retrieved successfully', [
            'appointments' => $results->sortByDesc('date_of_appointment')->values(),
            'count'        => $results->count(),
        ]);
    }

    /**
     * Update appointment status.
     * Body: { type: medical_test|home_xray, status: pending|confirmed|processing|finished|cancelled }
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'type'   => 'required|in:medical_test,home_xray',
            'status' => 'required|in:pending,confirmed,processing,finished,cancelled',
        ]);

        if ($validator->fails()) {
            return $this->error_response('Validation failed', $validator->errors());
        }

        $lab  = Auth::guard('lab-api')->user();
        $model = $request->type === 'medical_test' ? MedicalTest::class : HomeXray::class;

        $appointment = $model::where('id', $id)->where('lab_id', $lab->id)->first();

        if (!$appointment) {
            return $this->error_response('Appointment not found', null, 404);
        }

        $appointment->update(['status' => $request->status]);

        return $this->success_response('Status updated successfully', [
            'id'     => $appointment->id,
            'status' => $appointment->status,
        ]);
    }

    /**
     * Upload result files for an appointment.
     * Body (multipart): type, notes, files[] (images/PDFs)
     */
    public function uploadResult(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'type'    => 'required|in:medical_test,home_xray',
            'notes'   => 'nullable|string|max:2000',
            'files'   => 'nullable|array',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->error_response('Validation failed', $validator->errors());
        }

        $lab   = Auth::guard('lab-api')->user();
        $model = $request->type === 'medical_test' ? MedicalTest::class : HomeXray::class;

        $appointment = $model::where('id', $id)->where('lab_id', $lab->id)->first();

        if (!$appointment) {
            return $this->error_response('Appointment not found', null, 404);
        }

        DB::beginTransaction();
        try {
            $filenames = [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filenames[] = uploadImage('assets/admin/uploads/results', $file);
                }
            }

            $morphType = $request->type === 'medical_test' ? MedicalTest::class : HomeXray::class;

            $result = AppointmentResult::updateOrCreate(
                [
                    'appointment_id'   => $appointment->id,
                    'appointment_type' => $morphType,
                ],
                [
                    'lab_id'       => $lab->id,
                    'notes'        => $request->notes,
                    'files'        => array_merge($appointment->result?->files ?? [], $filenames),
                    'completed_at' => now(),
                ]
            );

            // Mark appointment as finished
            $appointment->update(['status' => 'finished']);

            DB::commit();

            return $this->success_response('Result uploaded successfully', [
                'result'    => $result,
                'file_urls' => $result->file_urls,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error_response('Failed to upload result', ['error' => $e->getMessage()]);
        }
    }

    // ─── Private formatters ───────────────────────────────────────────────────

    private function patientFromAppointment($appointment): ?array
    {
        // If appointment has a room, find the patient from that room
        if ($appointment->room) {
            $patient = $appointment->room->users()
                ->wherePivot('role', 'patient')
                ->first();

            if ($patient) {
                return [
                    'id'    => $patient->id,
                    'name'  => $patient->name,
                    'phone' => $patient->phone,
                ];
            }
        }

        // Fall back to the user who made the request
        return $appointment->user ? [
            'id'    => $appointment->user->id,
            'name'  => $appointment->user->name,
            'phone' => $appointment->user->phone,
        ] : null;
    }

    private function formatMedicalTest(MedicalTest $item): array
    {
        return [
            'id'                  => $item->id,
            'appointment_type'    => 'medical_test',
            'service_name'        => $item->typeMedicalTest->name ?? null,
            'date_of_appointment' => $item->date_of_appointment->format('Y-m-d'),
            'time_of_appointment' => $item->time_of_appointment?->format('H:i'),
            'note'                => $item->note,
            'address'             => $item->address,
            'status'              => $item->status,
            'status_name'         => $item->status_name,
            'room_code'           => $item->room?->code,
            'patient'             => $this->patientFromAppointment($item),
            'requester'           => $item->user ? ['id' => $item->user->id, 'name' => $item->user->name] : null,
            'result'              => $item->result ? [
                'notes'     => $item->result->notes,
                'file_urls' => $item->result->file_urls,
                'completed_at' => $item->result->completed_at?->format('Y-m-d H:i'),
            ] : null,
            'created_at'          => $item->created_at->format('Y-m-d H:i'),
        ];
    }

    private function formatHomeXray(HomeXray $item): array
    {
        $type = $item->typeHomeXray;
        return [
            'id'                  => $item->id,
            'appointment_type'    => 'home_xray',
            'service_name'        => $type ? ($type->isSubcategory() ? $type->full_name : $type->name) : null,
            'date_of_appointment' => $item->date_of_appointment->format('Y-m-d'),
            'time_of_appointment' => $item->time_of_appointment?->format('H:i'),
            'note'                => $item->note,
            'address'             => $item->address,
            'status'              => $item->status,
            'status_name'         => $item->status_name,
            'room_code'           => $item->room?->code,
            'patient'             => $this->patientFromAppointment($item),
            'requester'           => $item->user ? ['id' => $item->user->id, 'name' => $item->user->name] : null,
            'result'              => $item->result ? [
                'notes'        => $item->result->notes,
                'file_urls'    => $item->result->file_urls,
                'completed_at' => $item->result->completed_at?->format('Y-m-d H:i'),
            ] : null,
            'created_at'          => $item->created_at->format('Y-m-d H:i'),
        ];
    }
}
