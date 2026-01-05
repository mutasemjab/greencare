<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\TypeElderlyCare;
use App\Models\TypeHomeXray;
use App\Models\TypeMedicalTest;
use App\Models\ElderlyCare;
use App\Models\HomeXray;
use App\Models\MedicalTest;
use App\Models\RequestNurse;
use App\Models\Room;
use App\Models\TypeRequestNurse;
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
            // Get elderly care types grouped by care type
            $elderlyCareTypes = TypeElderlyCare::select('id', 'type_of_service', 'type_of_care', 'price')
                ->orderBy('type_of_care')
                ->orderBy('type_of_service')
                ->get()
                ->groupBy('type_of_care')
                ->map(function ($items, $careType) {
                    return [
                        'care_type' => $careType,
                        'care_type_name' => __('messages.' . $careType),
                        'services' => $items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => __('messages.' . $item->type_of_service),
                                'type_key' => $item->type_of_service,
                                'care_type' => $item->type_of_care,
                                'care_type_name' => __('messages.' . $item->type_of_care),
                                'price' => $item->price,
                                'formatted_price' => number_format($item->price, 2) . ' ' . __('messages.currency'),
                                'service_type' => 'elderly_care'
                            ];
                        })->values()
                    ];
                });

            $typeRequestNurse = TypeRequestNurse::select('id', 'type_of_service', 'price')
                ->orderBy('type_of_service')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => __('messages.' . $item->type_of_service),
                        'type_key' => $item->type_of_service,
                        'price' => $item->price,
                        'formatted_price' => number_format($item->price, 2) . ' ' . __('messages.currency'),
                        'service_type' => 'request_nurse'
                    ];
                });

            // Get home x-ray types in hierarchical structure
            $homeXrayTypes = $this->getHierarchicalXrayTypes();

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
                    'request_nurse' => $typeRequestNurse,
                    'home_xray_types' => $homeXrayTypes,
                    'medical_test_types' => $medicalTestTypes,
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
     * Get hierarchical x-ray types (main categories with their subcategories)
     */
    private function getHierarchicalXrayTypes()
    {
        // Get main categories with their children
        $mainCategories = TypeHomeXray::with(['children' => function ($query) {
            $query->orderBy('name');
        }])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $hierarchicalTypes = [];

        foreach ($mainCategories as $mainCategory) {
            $categoryData = [
                'id' => $mainCategory->id,
                'name' => $mainCategory->name,
                'price' => $mainCategory->price,
                'service_type' => 'home_xray',
                'is_main_category' => true,
                'subcategories_count' => $mainCategory->children->count(),
                'children' => []
            ];

            // Add subcategories if any
            if ($mainCategory->children->count() > 0) {
                $categoryData['children'] = $mainCategory->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'type_key' => null,
                        'price' => $child->price,
                        'formatted_price' => number_format($child->price, 2) . ' ' . __('messages.currency'),
                        'service_type' => 'home_xray',
                        'is_main_category' => false,
                        'parent_id' => $child->parent_id,
                        'parent_name' => $child->parent->name,
                    ];
                })->toArray();
            }

            $hierarchicalTypes[] = $categoryData;
        }

        // Also get standalone subcategories (in case some don't have parents due to data issues)
        $standaloneSubcategories = TypeHomeXray::whereNotNull('parent_id')
            ->whereDoesntHave('parent')
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'type_key' => null,
                    'price' => $item->price,
                    'formatted_price' => number_format($item->price, 2) . ' ' . __('messages.currency'),
                    'service_type' => 'home_xray',
                    'is_main_category' => false,
                    'parent_id' => $item->parent_id,
                    'parent_name' => 'Unknown Parent',
                    'is_orphaned' => true
                ];
            });

        // Add orphaned subcategories as separate items
        foreach ($standaloneSubcategories as $orphaned) {
            $hierarchicalTypes[] = $orphaned;
        }

        return $hierarchicalTypes;
    }



    /**
     * Validate room code and get discount
     */
    private function validateRoomCodeAndGetDiscount($roomCode)
    {
        if (empty($roomCode)) {
            return [
                'valid' => false,
                'discount' => 0,
                'room' => null
            ];
        }

        $room = Room::where('code', $roomCode)->first();

        if (!$room) {
            return [
                'valid' => false,
                'discount' => 0,
                'room' => null,
                'error' => __('messages.invalid_room_code')
            ];
        }

        return [
            'valid' => true,
            'discount' => $room->discount ?? 0,
            'room' => $room
        ];
    }

    /**
     * Calculate final price after discount
     */
    private function calculateFinalPrice($originalPrice, $discountPercent)
    {
        $discountAmount = ($originalPrice * $discountPercent) / 100;
        $finalPrice = $originalPrice - $discountAmount;

        return [
            'original_price' => $originalPrice,
            'discount_percent' => $discountPercent,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice
        ];
    }

    /**
     * Store a new appointment
     */
    public function storeAppointment(Request $request)
    {
        try {
            // Base validation rules
            $rules = [
                'service_type' => 'required|in:elderly_care,request_nurse,home_xray,medical_test',
                'date_of_appointment' => 'required|date|after_or_equal:today',
                'time_of_appointment' => 'nullable|date_format:H:i',
                'note' => 'nullable|string|max:1000',
                'room_code' => 'nullable|string|exists:rooms,code',
                'address' => 'nullable|string|max:500',
                'lat' => 'nullable|numeric|between:-90,90',
                'lng' => 'nullable|numeric|between:-180,180',
            ];

            // Add specific type validation based on service type
            switch ($request->service_type) {
                case 'elderly_care':
                    $rules['type_elderly_care_id'] = 'required|exists:type_elderly_cares,id';
                    break;
                case 'request_nurse':
                    $rules['type_request_nurse_id'] = 'required|exists:type_request_nurses,id';
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

            // Validate room code and get discount
            $roomValidation = $this->validateRoomCodeAndGetDiscount($request->room_code);

            if ($request->room_code && !$roomValidation['valid']) {
                return $this->error_response(
                    $roomValidation['error'] ?? __('messages.invalid_room_code'),
                    []
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
                'address' => $request->address,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'user_id' => $userId,
                'room_id' => $roomValidation['room']->id ?? null,
                'status' => 'pending',  
                'lab_id' => 1,          
            ];

            // Create appointment based on service type
            $appointment = null;
            $serviceInfo = null;

            switch ($request->service_type) {
                case 'elderly_care':
                    $appointmentData['type_elderly_care_id'] = $request->type_elderly_care_id;
                    $appointment = ElderlyCare::create($appointmentData);
                    $appointment->load(['user', 'typeElderlyCare', 'room']);
                    $serviceInfo = $appointment->typeElderlyCare;
                    break;
                case 'request_nurse':
                    $appointmentData['type_request_nurse_id'] = $request->type_request_nurse_id;
                    $appointment = RequestNurse::create($appointmentData);
                    $appointment->load(['user', 'typeRequestNurse', 'room']);
                    $serviceInfo = $appointment->typeRequestNurse;
                    break;

                case 'home_xray':
                    $appointmentData['type_home_xray_id'] = $request->type_home_xray_id;
                    $appointment = HomeXray::create($appointmentData);
                    $appointment->load(['user', 'typeHomeXray.parent', 'room']);
                    $serviceInfo = $appointment->typeHomeXray;
                    break;

                case 'medical_test':
                    $appointmentData['type_medical_test_id'] = $request->type_medical_test_id;
                    $appointment = MedicalTest::create($appointmentData);
                    $appointment->load(['user', 'typeMedicalTest', 'room']);
                    $serviceInfo = $appointment->typeMedicalTest;
                    break;
            }

            // Calculate pricing with discount
            $originalPrice = $serviceInfo->price ?? 0;
            $discountPercent = $roomValidation['discount'] ?? 0;
            $priceCalculation = $this->calculateFinalPrice($originalPrice, $discountPercent);

            // Format response data with enhanced x-ray info
            $serviceDisplayName = $serviceInfo->name ?? __('messages.' . ($serviceInfo->type_of_service ?? 'unknown'));

            // For elderly care, include care type information
            if ($request->service_type === 'elderly_care' && $serviceInfo) {
                $serviceDisplayName = __('messages.' . $serviceInfo->type_of_service);
            }

            // For x-ray types, include hierarchy information
            if ($request->service_type === 'home_xray' && $serviceInfo) {
                $serviceDisplayName = $serviceInfo->isSubcategory() ? $serviceInfo->full_name : $serviceInfo->name;
            }

            $responseData = [
                'appointment' => [
                    'id' => $appointment->id,
                    'service_type' => $request->service_type,
                    'service_name' => $serviceDisplayName,
                    'original_price' => $priceCalculation['original_price'],
                    'discount_percent' => $priceCalculation['discount_percent'],
                    'discount_amount' => $priceCalculation['discount_amount'],
                    'final_price' => $priceCalculation['final_price'],
                    'formatted_original_price' => number_format($priceCalculation['original_price'], 2) . ' ' . __('messages.currency'),
                    'formatted_discount_amount' => number_format($priceCalculation['discount_amount'], 2) . ' ' . __('messages.currency'),
                    'formatted_final_price' => number_format($priceCalculation['final_price'], 2) . ' ' . __('messages.currency'),
                    'date_of_appointment' => $appointment->date_of_appointment->format('Y-m-d'),
                    'time_of_appointment' => $appointment->time_of_appointment ? $appointment->time_of_appointment->format('H:i') : null,
                    'note' => $appointment->note,
                    'address' => $appointment->address,
                    'location' => [
                        'lat' => $appointment->lat,
                        'lng' => $appointment->lng,
                    ],
                    'room_info' => $appointment->room ? [
                        'id' => $appointment->room->id,
                        'code' => $appointment->room->code,
                        'name' => $appointment->room->name ?? null,
                        'discount' => $appointment->room->discount
                    ] : null,
                    'user' => [
                        'id' => $appointment->user->id,
                        'name' => $appointment->user->name,
                        'email' => $appointment->user->email,
                    ],
                    'created_at' => $appointment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $appointment->updated_at->format('Y-m-d H:i:s'),
                ]
            ];

            // Add care type information for elderly care appointments
            if ($request->service_type === 'elderly_care' && $serviceInfo) {
                $responseData['appointment']['care_info'] = [
                    'care_type' => $serviceInfo->type_of_care,
                    'care_type_name' => __('messages.' . $serviceInfo->type_of_care),
                    'service_type' => $serviceInfo->type_of_service,
                    'service_type_name' => __('messages.' . $serviceInfo->type_of_service),
                ];
            }

            // Add hierarchy information for x-ray appointments
            if ($request->service_type === 'home_xray' && $serviceInfo) {
                $responseData['appointment']['hierarchy_info'] = [
                    'is_main_category' => $serviceInfo->isMainCategory(),
                    'is_subcategory' => $serviceInfo->isSubcategory(),
                    'parent_id' => $serviceInfo->parent_id,
                    'parent_name' => $serviceInfo->parent ? $serviceInfo->parent->name : null,
                    'full_path' => $serviceInfo->full_name
                ];
            }


            // ============================================
            // Send notifications
            // ============================================

            // Send notification to the user who created the appointment
            $this->sendAppointmentCreatedNotification(
                $appointment,
                $request->service_type,
                $serviceDisplayName,
                $priceCalculation['final_price']
            );

            // Send notification to room members if room code was provided
            if ($request->room_code && isset($roomValidation['room'])) {
                $this->sendAppointmentToRoomNotification(
                    $appointment,
                    $roomValidation['room'],
                    $request->service_type,
                    $serviceDisplayName,
                    $priceCalculation['final_price']
                );
            }

            // ============================================


            if ($request->room_code && isset($roomValidation['room'])) {
                try {
                    $room = $roomValidation['room'];

                    // Extract only serializable data
                    $messageData = [
                        'room_id' => $room->id,
                        'room_code' => $room->code,
                        'user_id' => $userId,
                        'user_name' => $appointment->user->name,
                        'user_photo' => $appointment->user->photo ?? 'null',
                        'appointment_id' => $appointment->id,
                        'service_name' => $serviceDisplayName,
                        'date_of_appointment' => $appointment->date_of_appointment->format('Y-m-d H:i:s'),
                        'time_of_appointment' => $appointment->time_of_appointment ? $appointment->time_of_appointment->format('H:i:s') : null,
                        'note' => $appointment->note,
                        'address' => $appointment->address,
                        'lat' => $appointment->lat,
                        'lng' => $appointment->lng,
                        'final_price' => $priceCalculation['final_price'],
                        'discount_percent' => $priceCalculation['discount_percent'],
                        'discount_amount' => $priceCalculation['discount_amount'],
                    ];

                    // Add care type for elderly care
                    if ($request->service_type === 'elderly_care' && $serviceInfo) {
                        $messageData['care_type'] = __('messages.' . $serviceInfo->type_of_care);
                    }

                    dispatch(function () use ($messageData) {
                        try {
                            $projectId = config('firebase.project_id');
                            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents";

                            // Generate 20 character random ID
                            $messageId = \Illuminate\Support\Str::random(20);

                            // Format appointment message
                            $messageText = "📅 New Appointment Booked\n\n";

                            // Add care type if available
                            if (isset($messageData['care_type'])) {
                                $messageText .= "Care Type: {$messageData['care_type']}\n";
                            }

                            $messageText .= "Service: {$messageData['service_name']}\n";
                            $messageText .= "Date: " . \Carbon\Carbon::parse($messageData['date_of_appointment'])->format('M d, Y') . "\n";

                            if ($messageData['time_of_appointment']) {
                                $messageText .= "Time: " . \Carbon\Carbon::parse($messageData['time_of_appointment'])->format('h:i A') . "\n";
                            }

                            // Add address if available
                            if ($messageData['address']) {
                                $messageText .= "Address: {$messageData['address']}\n";
                            }

                            // Add location coordinates if available
                            if ($messageData['lat'] && $messageData['lng']) {
                                $messageText .= "Location: {$messageData['lat']}, {$messageData['lng']}\n";
                            }

                            $messageText .= "Price: JD" . number_format($messageData['final_price'], 2) . "\n";

                            if ($messageData['discount_percent'] > 0) {
                                $messageText .= "Discount: " . $messageData['discount_percent'] . "% (-JD" . number_format($messageData['discount_amount'], 2) . ")\n";
                            }

                            if ($messageData['note']) {
                                $messageText .= "\nNote: " . $messageData['note'];
                            }

                            // Prepare message data
                            $firestoreData = [
                                'fields' => [
                                    'created_at' => ['timestampValue' => now()->toIso8601String()],
                                    'id' => ['stringValue' => $messageId],
                                    'is_delivered' => ['booleanValue' => false],
                                    'is_read' => ['booleanValue' => false],
                                    'media_url' => ['stringValue' => ''],
                                    'reply_to' => ['stringValue' => ''],
                                    'sender_avatar' => ['stringValue' => $messageData['user_photo']],
                                    'sender_id' => ['integerValue' => (string)$messageData['user_id']],
                                    'sender_name' => ['stringValue' => $messageData['user_name']],
                                    'text' => ['stringValue' => $messageText],
                                    'type' => ['stringValue' => 'appointment'],
                                ]
                            ];

                            // Add location data if available
                            if ($messageData['lat'] && $messageData['lng']) {
                                $firestoreData['fields']['location'] = [
                                    'mapValue' => [
                                        'fields' => [
                                            'lat' => ['doubleValue' => (float)$messageData['lat']],
                                            'lng' => ['doubleValue' => (float)$messageData['lng']],
                                            'address' => ['stringValue' => $messageData['address'] ?? '']
                                        ]
                                    ]
                                ];
                            }

                            // Send to Firestore messages subcollection
                            $response = \Illuminate\Support\Facades\Http::timeout(10)->patch(
                                "{$baseUrl}/rooms/room_{$messageData['room_id']}/messages/{$messageId}",
                                $firestoreData
                            );

                            if ($response->successful()) {
                                \Log::info("Appointment message sent to room: {$messageData['room_code']} (ID: {$messageData['room_id']})");

                                // Update room's last_message
                                $roomUpdate = [
                                    'fields' => [
                                        'last_message' => ['stringValue' => $messageText],
                                        'last_message_at' => ['timestampValue' => now()->toIso8601String()],
                                    ]
                                ];

                                \Illuminate\Support\Facades\Http::timeout(10)->patch(
                                    "{$baseUrl}/rooms/room_{$messageData['room_id']}?updateMask.fieldPaths=last_message&updateMask.fieldPaths=last_message_at",
                                    $roomUpdate
                                );
                            } else {
                                \Log::error('Failed to send appointment message', [
                                    'room_code' => $messageData['room_code'],
                                    'status' => $response->status(),
                                    'body' => $response->body()
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::error('Failed to send appointment message: ' . $e->getMessage());
                        }
                    })->afterResponse();
                } catch (\Exception $e) {
                    \Log::error('Failed to dispatch appointment message: ' . $e->getMessage());
                }
            }


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


    public function getAppointmentsByType(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->error_response(__('messages.user_not_authenticated'), []);
            }

            $request->validate([
                'service_type' => 'required|in:elderly_care,request_nurse,home_xray,medical_test',
            ]);

            $serviceType = $request->service_type;
            $appointments = [];

            switch ($serviceType) {
                case 'elderly_care':
                    $appointments = ElderlyCare::with(['typeElderlyCare'])
                        ->where('user_id', $user->id)
                        ->orderBy('date_of_appointment', 'desc')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'service_type' => 'elderly_care',
                                'service_name' => __('messages.' . $item->typeElderlyCare->type_of_service),
                                'care_type' => $item->typeElderlyCare->type_of_care,
                                'care_type_name' => __('messages.' . $item->typeElderlyCare->type_of_care),
                                'service_type_key' => $item->typeElderlyCare->type_of_service,
                                'service_type_name' => __('messages.' . $item->typeElderlyCare->type_of_service),
                                'date_of_appointment' => $item->date_of_appointment->format('Y-m-d'),
                                'time_of_appointment' => $item->time_of_appointment ? $item->time_of_appointment->format('H:i') : null,
                                'note' => $item->note,
                                'address' => $item->address,
                                'location' => [
                                    'lat' => $item->lat,
                                    'lng' => $item->lng,
                                ],
                                'price' => $item->typeElderlyCare->price ?? 0,
                                'formatted_price' => number_format($item->typeElderlyCare->price ?? 0, 2) . ' ' . __('messages.currency'),
                            ];
                        });
                    break;

                case 'request_nurse':
                    $appointments = RequestNurse::with(['typeRequestNurse'])
                        ->where('user_id', $user->id)
                        ->orderBy('date_of_appointment', 'desc')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'service_type' => 'request_nurse',
                                'service_name' => __('messages.' . $item->typeRequestNurse->type_of_service),
                                'service_type_key' => $item->typeRequestNurse->type_of_service,
                                'date_of_appointment' => $item->date_of_appointment->format('Y-m-d'),
                                'time_of_appointment' => $item->time_of_appointment ? $item->time_of_appointment->format('H:i') : null,
                                'note' => $item->note,
                                'address' => $item->address,
                                'location' => [
                                    'lat' => $item->lat,
                                    'lng' => $item->lng,
                                ],
                                'price' => $item->typeRequestNurse->price ?? 0,
                                'formatted_price' => number_format($item->typeRequestNurse->price ?? 0, 2) . ' ' . __('messages.currency'),
                            ];
                        });
                    break;

                case 'home_xray':
                    $appointments = HomeXray::with(['typeHomeXray.parent'])
                        ->where('user_id', $user->id)
                        ->orderBy('date_of_appointment', 'desc')
                        ->get()
                        ->map(function ($item) {
                            $type = $item->typeHomeXray;
                            return [
                                'id' => $item->id,
                                'service_type' => 'home_xray',
                                'service_name' => $type ? ($type->isSubcategory() ? $type->full_name : $type->name) : null,
                                'date_of_appointment' => $item->date_of_appointment->format('Y-m-d'),
                                'time_of_appointment' => $item->time_of_appointment ? $item->time_of_appointment->format('H:i') : null,
                                'note' => $item->note,
                                'address' => $item->address,
                                'location' => [
                                    'lat' => $item->lat,
                                    'lng' => $item->lng,
                                ],
                                'price' => $type->price ?? 0,
                                'formatted_price' => number_format($type->price ?? 0, 2) . ' ' . __('messages.currency'),
                                'parent_name' => $type->parent->name ?? null,
                                'is_subcategory' => $type ? $type->isSubcategory() : false,
                            ];
                        });
                    break;

                case 'medical_test':
                    $appointments = MedicalTest::with(['typeMedicalTest'])
                        ->where('user_id', $user->id)
                        ->orderBy('date_of_appointment', 'desc')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'service_type' => 'medical_test',
                                'service_name' => $item->typeMedicalTest->name ?? null,
                                'date_of_appointment' => $item->date_of_appointment->format('Y-m-d'),
                                'time_of_appointment' => $item->time_of_appointment ? $item->time_of_appointment->format('H:i') : null,
                                'note' => $item->note,
                                'address' => $item->address,
                                'location' => [
                                    'lat' => $item->lat,
                                    'lng' => $item->lng,
                                ],
                                'price' => $item->typeMedicalTest->price ?? 0,
                                'formatted_price' => number_format($item->typeMedicalTest->price ?? 0, 2) . ' ' . __('messages.currency'),
                            ];
                        });
                    break;
            }

            return $this->success_response(
                __('messages.appointments_fetched_successfully'),
                ['appointments' => $appointments]
            );
        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_fetching_appointments'),
                ['error' => $e->getMessage()]
            );
        }
    }
}
