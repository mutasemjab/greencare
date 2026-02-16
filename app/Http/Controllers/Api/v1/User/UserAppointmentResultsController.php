<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\MedicalTest;
use App\Models\HomeXray;
use App\Models\Room;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserAppointmentResultsController extends Controller
{
    use Responses;

    /**
     * Get user's appointment results within a specific room
     */
    public function getRoomAppointmentResults(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'room_id' => 'required|exists:rooms,id',
                'type' => 'nullable|in:medical_test,home_xray,all',
                'status' => 'nullable|in:pending,confirmed,processing,finished,cancelled',
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            $roomId = $request->room_id;
            $type = $request->get('type', 'all');
            $status = $request->get('status');

            // Verify user is in the room
            $room = Room::find($roomId);
            $userInRoom = $room->users()->where('user_id', $user->id)->exists();

            if (!$userInRoom) {
                return $this->error_response(__('messages.unauthorized_access'), []);
            }

            $appointments = collect();

            // Get Medical Tests for this user in this room
            if ($type === 'all' || $type === 'medical_test') {
                $medicalTests = MedicalTest::with(['typeMedicalTest', 'lab', 'result'])
                    ->where('user_id', $user->id)
                    ->where('room_id', $roomId)
                    ->when($status, function ($query) use ($status) {
                        return $query->where('status', $status);
                    })
                    ->orderBy('date_of_appointment', 'desc')
                    ->orderBy('time_of_appointment', 'desc')
                    ->get()
                    ->map(function ($item) {
                        return $this->formatUserAppointment($item, 'medical_test');
                    });

                $appointments = $appointments->merge($medicalTests);
            }

            // Get Home X-rays for this user in this room
            if ($type === 'all' || $type === 'home_xray') {
                $homeXrays = HomeXray::with(['typeHomeXray', 'lab', 'result'])
                    ->where('user_id', $user->id)
                    ->where('room_id', $roomId)
                    ->when($status, function ($query) use ($status) {
                        return $query->where('status', $status);
                    })
                    ->orderBy('date_of_appointment', 'desc')
                    ->orderBy('time_of_appointment', 'desc')
                    ->get()
                    ->map(function ($item) {
                        return $this->formatUserAppointment($item, 'home_xray');
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
                    'room' => [
                        'id' => $room->id,
                        'title' => $room->title,
                        'code' => $room->code,
                    ],
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
     * Get all user's appointment results (across all rooms)
     */
    public function getAllUserAppointmentResults(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'type' => 'nullable|in:medical_test,home_xray,all',
                'status' => 'nullable|in:pending,confirmed,processing,finished,cancelled',
                'with_room' => 'nullable|boolean', // Include room details
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            $type = $request->get('type', 'all');
            $status = $request->get('status');
            $withRoom = $request->get('with_room', true);

            $appointments = collect();

            // Get Medical Tests
            if ($type === 'all' || $type === 'medical_test') {
                $medicalTestsQuery = MedicalTest::with(['typeMedicalTest', 'lab', 'result'])
                    ->where('user_id', $user->id);

                if ($withRoom) {
                    $medicalTestsQuery->with('room:id,title,code');
                }

                $medicalTests = $medicalTestsQuery
                    ->when($status, function ($query) use ($status) {
                        return $query->where('status', $status);
                    })
                    ->orderBy('date_of_appointment', 'desc')
                    ->orderBy('time_of_appointment', 'desc')
                    ->get()
                    ->map(function ($item) use ($withRoom) {
                        return $this->formatUserAppointment($item, 'medical_test', $withRoom);
                    });

                $appointments = $appointments->merge($medicalTests);
            }

            // Get Home X-rays
            if ($type === 'all' || $type === 'home_xray') {
                $homeXraysQuery = HomeXray::with(['typeHomeXray', 'lab', 'result'])
                    ->where('user_id', $user->id);

                if ($withRoom) {
                    $homeXraysQuery->with('room:id,title,code');
                }

                $homeXrays = $homeXraysQuery
                    ->when($status, function ($query) use ($status) {
                        return $query->where('status', $status);
                    })
                    ->orderBy('date_of_appointment', 'desc')
                    ->orderBy('time_of_appointment', 'desc')
                    ->get()
                    ->map(function ($item) use ($withRoom) {
                        return $this->formatUserAppointment($item, 'home_xray', $withRoom);
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
                    'total' => $appointments->count(),
                    'summary' => [
                        'pending' => $appointments->where('status', 'pending')->count(),
                        'confirmed' => $appointments->where('status', 'confirmed')->count(),
                        'processing' => $appointments->where('status', 'processing')->count(),
                        'finished' => $appointments->where('status', 'finished')->count(),
                        'cancelled' => $appointments->where('status', 'cancelled')->count(),
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
     * Helper: Format appointment data for user view
     */
    private function formatUserAppointment($appointment, $type, $includeRoom = false)
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

        // Add lab information
        if ($appointment->lab) {
            $data['lab'] = [
                'id' => $appointment->lab->id,
                'name' => $appointment->lab->name,
                'phone' => $appointment->lab->phone,
                'address' => $appointment->lab->address,
            ];
        }

        // Add room info if requested
        if ($includeRoom && $appointment->room) {
            $data['room'] = [
                'id' => $appointment->room->id,
                'title' => $appointment->room->title,
                'code' => $appointment->room->code,
            ];
        }

        // Add result info if available
        if ($appointment->result) {
            $data['result'] = [
                'id' => $appointment->result->id,
                'files' => $appointment->result->file_urls,
                'notes' => $appointment->result->notes,
                'completed_at' => $appointment->result->completed_at ? 
                    $appointment->result->completed_at->format('Y-m-d H:i:s') : null,
            ];
        } else {
            $data['result'] = null;
        }

        return $data;
    }
}