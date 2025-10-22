<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\AppointmentProvider;
use App\Models\Provider;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AppointmentProviderController extends Controller
{
    use Responses;
    /**
     * Store a new appointment provider.
     */
     public function store(Request $request)
    {
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'name_of_patient' => 'required|string|max:255',
                'phone_of_patient' => 'required|string|max:20',
                'address' => 'nullable|string|max:500',
                'lat' => 'nullable|numeric|between:-90,90',
                'lng' => 'nullable|numeric|between:-180,180',
                'description' => 'nullable|string|max:1000',
                'date_of_appointment' => 'nullable|date|after_or_equal:today',
                'time_of_appointment' => 'nullable|date_format:H:i',
                'provider_id' => 'required|integer|exists:providers,id',
                'user_id' => 'nullable|integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    $validator->errors()
                );
            }

            // Check if provider exists
            $provider = Provider::find($request->provider_id);
            if (!$provider) {
                return $this->error_response(
                    __('messages.provider_not_found'),
                    []
                );
            }

            // Use authenticated user ID if user_id is not provided
            $userId = $request->user_id ?? Auth::id();
            
            if (!$userId) {
                return $this->error_response(
                    __('messages.user_required'),
                    []
                );
            }

            // Check if user exists
            $user = User::find($userId);
            if (!$user) {
                return $this->error_response(
                    __('messages.user_not_found'),
                    []
                );
            }

            // Create the appointment
            $appointment = AppointmentProvider::create([
                'name_of_patient' => $request->name_of_patient,
                'phone_of_patient' => $request->phone_of_patient,
                'address' => $request->address,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'description' => $request->description,
                'date_of_appointment' => $request->date_of_appointment,
                'time_of_appointment' => $request->time_of_appointment,
                'provider_id' => $request->provider_id,
                'user_id' => $userId,
                'status' =>  AppointmentProvider::STATUS_PENDING
            ]);

            // Load relationships for response
            $appointment->load(['provider', 'user']);

            // Format response data
            $appointmentData = $this->formatAppointmentData($appointment);

            return $this->success_response(
                __('messages.appointment_created_successfully'),
                $appointmentData
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_creating_appointment'),
                ['error' => $e->getMessage()]
            );
        }
    }


   public function getUserAppointments(Request $request)
    {
        try {
            $userId = $request->user_id ?? Auth::id();
            
            if (!$userId) {
                return $this->error_response(
                    __('messages.user_required'),
                    []
                );
            }

            $appointments = AppointmentProvider::with(['provider', 'user'])
                ->where('user_id', $userId)
                ->orderBy('date_of_appointment', 'desc')
                ->orderBy('time_of_appointment', 'desc')
                ->get();

            $appointmentsData = $appointments->map(function ($appointment) {
                return $this->formatAppointmentData($appointment);
            });

            return $this->success_response(
                __('messages.appointments_retrieved_successfully'),
                $appointmentsData
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_retrieving_appointments'),
                []
            );
        }
    }

     private function formatAppointmentData($appointment)
    {
        return [
            'id' => $appointment->id,
            'name_of_patient' => $appointment->name_of_patient,
            'phone_of_patient' => $appointment->phone_of_patient,
            'address' => $appointment->address,
            'lat' => $appointment->lat,
            'lng' => $appointment->lng,
            'description' => $appointment->description,
            'date_of_appointment' => $appointment->date_of_appointment ? $appointment->date_of_appointment->format('Y-m-d') : null,
            'time_of_appointment' => $appointment->time_of_appointment ? $appointment->time_of_appointment->format('H:i') : null,
            'formatted_appointment' => $appointment->formatted_appointment,
            'status' => $appointment->status,
            'status_name' => $appointment->status_name,
            'status_badge_class' => $appointment->status_badge_class,
            'provider' => [
                'id' => $appointment->provider->id,
                'name' => $appointment->provider->name,
                'experience' => $appointment->provider->number_years_experience,
                'description' => $appointment->provider->description,
                'price' => $appointment->provider->price,
                'photo' => $appointment->provider->photo ? asset('assets/admin/uploads/' . $appointment->provider->photo) : null,
                'rating' => $appointment->provider->rating,
            ],
            'user' => [
                'id' => $appointment->user->id,
                'name' => $appointment->user->name,
                'email' => $appointment->user->email,
            ],
            'created_at' => $appointment->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $appointment->updated_at->format('Y-m-d H:i:s'),
        ];
    }

   

}