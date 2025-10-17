<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\TypeElderlyCare;
use App\Models\TypeHomeXray;
use App\Models\TypeMedicalTest;
use App\Models\ElderlyCare;
use App\Models\HomeXray;
use App\Models\MedicalTest;
use App\Models\Room;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AppointmentApiController extends Controller
{
    use Responses;

    /**
     * Get all service types information for appointments
     */
    public function getServiceTypes()
    {
        try {
            // Get elderly care types
            $elderlyCareTypes = TypeElderlyCare::select('id', 'type_of_service', 'price')
                ->orderBy('type_of_service')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => __('messages.' . $item->type_of_service),
                        'type_key' => $item->type_of_service,
                        'price' => $item->price,
                        'formatted_price' => number_format($item->price, 2) . ' ' . __('messages.currency'),
                        'service_type' => 'elderly_care'
                    ];
                });

            // Get home x-ray types
            $homeXrayTypes = TypeHomeXray::select('id', 'name', 'price')
                ->orderBy('name')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'type_key' => null,
                        'price' => $item->price,
                        'formatted_price' => number_format($item->price, 2) . ' ' . __('messages.currency'),
                        'service_type' => 'home_xray'
                    ];
                });

            // Get medical test types
            $medicalTestTypes = TypeMedicalTest::select('id', 'name', 'price')
                ->orderBy('name')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'type_key' => null,
                        'price' => $item->price,
                        'formatted_price' => number_format($item->price, 2) . ' ' . __('messages.currency'),
                        'service_type' => 'medical_test'
                    ];
                });

            // Get available rooms
            $rooms = Room::get();

            return $this->success_response(
                __('messages.service_types_retrieved_successfully'),
                [
                    'elderly_care_types' => $elderlyCareTypes,
                    'home_xray_types' => $homeXrayTypes,
                    'medical_test_types' => $medicalTestTypes,
                    'rooms' => $rooms,
                    'service_categories' => [
                        [
                            'key' => 'elderly_care',
                            'name' => __('messages.elderly_care'),
                            'description' => __('messages.elderly_care_description')
                        ],
                        [
                            'key' => 'home_xray',
                            'name' => __('messages.home_xray'),
                            'description' => __('messages.home_xray_description')
                        ],
                        [
                            'key' => 'medical_test',
                            'name' => __('messages.medical_test'),
                            'description' => __('messages.medical_test_description')
                        ]
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
     * Store a new appointment
     */
    public function storeAppointment(Request $request)
    {
        try {
            // Base validation rules
            $rules = [
                'service_type' => 'required|in:elderly_care,home_xray,medical_test',
                'date_of_appointment' => 'required|date|after_or_equal:today',
                'time_of_appointment' => 'nullable|date_format:H:i',
                'note' => 'nullable|string|max:1000',
            ];

            // Add specific type validation based on service type
            switch ($request->service_type) {
                case 'elderly_care':
                    $rules['type_elderly_care_id'] = 'required|exists:type_elderly_cares,id';
                    break;
                case 'home_xray':
                    $rules['type_home_xray_id'] = 'required|exists:type_home_xrays,id';
                    break;
                case 'medical_test':
                    $rules['type_medical_test_id'] = 'required|exists:type_medical_tests,id';
                    break;
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            // Get authenticated user ID
            $userId = Auth::id();
            if (!$userId) {
                return $this->error_response(
                    __('messages.user_not_authenticated'),
                    []
                );
            }

            // Prepare appointment data
            $appointmentData = [
                'date_of_appointment' => $request->date_of_appointment,
                'time_of_appointment' => $request->time_of_appointment,
                'note' => $request->note,
                'user_id' => $userId,
            ];

            // Create appointment based on service type
            $appointment = null;
            $serviceInfo = null;

            switch ($request->service_type) {
                case 'elderly_care':
                    $appointmentData['type_elderly_care_id'] = $request->type_elderly_care_id;
                    $appointment = ElderlyCare::create($appointmentData);
                    $appointment->load(['user', 'typeElderlyCare']);
                    $serviceInfo = $appointment->typeElderlyCare;
                    break;

                case 'home_xray':
                    $appointmentData['type_home_xray_id'] = $request->type_home_xray_id;
                    $appointment = HomeXray::create($appointmentData);
                    $appointment->load(['user', 'typeHomeXray']);
                    $serviceInfo = $appointment->typeHomeXray;
                    break;

                case 'medical_test':
                    $appointmentData['type_medical_test_id'] = $request->type_medical_test_id;
                    $appointment = MedicalTest::create($appointmentData);
                    $appointment->load(['user', 'typeMedicalTest']);
                    $serviceInfo = $appointment->typeMedicalTest;
                    break;
            }

            // Format response data
            $responseData = [
                'appointment' => [
                    'id' => $appointment->id,
                    'service_type' => $request->service_type,
                    'service_name' => $serviceInfo->name ?? __('messages.' . ($serviceInfo->type_of_service ?? 'unknown')),
                    'price' => $serviceInfo->price ?? 0,
                    'formatted_price' => number_format($serviceInfo->price ?? 0, 2) . ' ' . __('messages.currency'),
                    'date_of_appointment' => $appointment->date_of_appointment->format('Y-m-d'),
                    'time_of_appointment' => $appointment->time_of_appointment ? $appointment->time_of_appointment->format('H:i') : null,
                    'note' => $appointment->note,
                    'user' => [
                        'id' => $appointment->user->id,
                        'name' => $appointment->user->name,
                        'email' => $appointment->user->email,
                    ],
                    'created_at' => $appointment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $appointment->updated_at->format('Y-m-d H:i:s'),
                ]
            ];

            return $this->success_response(
                __('messages.appointment_created_successfully'),
                $responseData
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_creating_appointment'),
                ['error' => $e->getMessage()]
            );
        }
    }
}