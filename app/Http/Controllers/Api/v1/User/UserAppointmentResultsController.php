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
     * Get user's appointment results
     * - With room_id: returns results for that specific room
     * - Without room_id: returns all results across all rooms
     */
    public function getAppointmentResults(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'room_id' => 'nullable|exists:rooms,id',
                'type' => 'nullable|in:medical_test,home_xray,all',
                'status' => 'nullable|in:pending,confirmed,processing,finished,cancelled',
                'with_room' => 'nullable|boolean', // Include room details (only used when room_id is not provided)
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            $roomId = $request->get('room_id');
            $type = $request->get('type', 'all');
            $status = $request->get('status');
            $withRoom = $request->get('with_room', true);

            // If room_id is provided, verify user is in the room
            if ($roomId) {
                $room = Room::find($roomId);
                $userInRoom = $room->users()->where('user_id', $user->id)->exists();

                if (!$userInRoom) {
                    return $this->error_response(__('messages.unauthorized_access'), []);
                }
            }

            $appointments = collect();

            // Get Medical Tests
            if ($type === 'all' || $type === 'medical_test') {
                $medicalTestsQuery = MedicalTest::with(['typeMedicalTest', 'lab', 'result'])
                    ->where('user_id', $user->id);

                // Filter by room if room_id provided
                if ($roomId) {
                    $medicalTestsQuery->where('room_id', $roomId);
                } elseif ($withRoom) {
                    // Include room details if requested and no specific room
                    $medicalTestsQuery->with('room:id,title,code');
                }

                $medicalTests = $medicalTestsQuery
                    ->when($status, function ($query) use ($status) {
                        return $query->where('status', $status);
                    })
                    ->orderBy('date_of_appointment', 'desc')
                    ->orderBy('time_of_appointment', 'desc')
                    ->get()
                    ->map(function ($item) use ($roomId, $withRoom) {
                        return $this->formatUserAppointment($item, 'medical_test', !$roomId && $withRoom);
                    });

                $appointments = $appointments->merge($medicalTests);
            }

            // Get Home X-rays
            if ($type === 'all' || $type === 'home_xray') {
                $homeXraysQuery = HomeXray::with(['typeHomeXray', 'lab', 'result'])
                    ->where('user_id', $user->id);

                // Filter by room if room_id provided
                if ($roomId) {
                    $homeXraysQuery->where('room_id', $roomId);
                } elseif ($withRoom) {
                    // Include room details if requested and no specific room
                    $homeXraysQuery->with('room:id,title,code');
                }

                $homeXrays = $homeXraysQuery
                    ->when($status, function ($query) use ($status) {
                        return $query->where('status', $status);
                    })
                    ->orderBy('date_of_appointment', 'desc')
                    ->orderBy('time_of_appointment', 'desc')
                    ->get()
                    ->map(function ($item) use ($roomId, $withRoom) {
                        return $this->formatUserAppointment($item, 'home_xray', !$roomId && $withRoom);
                    });

                $appointments = $appointments->merge($homeXrays);
            }

            // Sort by date if showing all types
            if ($type === 'all') {
                $appointments = $appointments->sortByDesc('date_of_appointment')->values();
            }

            // Build response
            $response = [
                'appointments' => $appointments,
                'total' => $appointments->count(),
            ];

            // Add room info if room_id was provided
            if ($roomId) {
                $response['room'] = [
                    'id' => $room->id,
                    'title' => $room->title,
                    'code' => $room->code,
                ];
            } else {
                // Add summary statistics for all appointments
                $response['summary'] = [
                    'pending' => $appointments->where('status', 'pending')->count(),
                    'confirmed' => $appointments->where('status', 'confirmed')->count(),
                    'processing' => $appointments->where('status', 'processing')->count(),
                    'finished' => $appointments->where('status', 'finished')->count(),
                    'cancelled' => $appointments->where('status', 'cancelled')->count(),
                ];
            }

            return $this->success_response(
                __('messages.appointments_fetched_successfully'),
                $response
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