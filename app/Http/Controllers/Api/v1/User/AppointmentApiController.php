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
        $mainCategories = TypeHomeXray::with(['children' => function($query) {
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
                case 'request_nurse':
                    $appointmentData['type_request_nurse_id'] = $request->type_request_nurse_id;
                    $appointment = RequestNurse::create($appointmentData);
                    $appointment->load(['user', 'typeRequestNurse']);
                    $serviceInfo = $appointment->typeRequestNurse;
                    break;

                case 'home_xray':
                    $appointmentData['type_home_xray_id'] = $request->type_home_xray_id;
                    $appointment = HomeXray::create($appointmentData);
                    $appointment->load(['user', 'typeHomeXray.parent']);
                    $serviceInfo = $appointment->typeHomeXray;
                    break;

                case 'medical_test':
                    $appointmentData['type_medical_test_id'] = $request->type_medical_test_id;
                    $appointment = MedicalTest::create($appointmentData);
                    $appointment->load(['user', 'typeMedicalTest']);
                    $serviceInfo = $appointment->typeMedicalTest;
                    break;
            }

            // Format response data with enhanced x-ray info
            $serviceDisplayName = $serviceInfo->name ?? __('messages.' . ($serviceInfo->type_of_service ?? 'unknown'));
            
            // For x-ray types, include hierarchy information
            if ($request->service_type === 'home_xray' && $serviceInfo) {
                $serviceDisplayName = $serviceInfo->isSubcategory() ? $serviceInfo->full_name : $serviceInfo->name;
            }

            $responseData = [
                'appointment' => [
                    'id' => $appointment->id,
                    'service_type' => $request->service_type,
                    'service_name' => $serviceDisplayName,
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
