<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\PledgeForm;
use App\Models\Room;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PledgeFormController extends Controller
{
    use Responses;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = PledgeForm::with('room');
            
            // Filter by type if provided
            if ($request->has('type') && in_array($request->type, ['pledge_form', 'authorization_form'])) {
                $query->where('type', $request->type);
            }
            
            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name_of_nurse', 'like', "%{$search}%")
                      ->orWhere('name_of_patient', 'like', "%{$search}%")
                      ->orWhere('identity_number_of_patient', 'like', "%{$search}%")
                      ->orWhere('phone_of_patient', 'like', "%{$search}%");
                });
            }
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $pledgeForms = $query->orderBy('created_at', 'desc')->paginate($perPage);
            
            return $this->success_response(
                __('messages.data_retrieved_successfully'),
                [
                    'pledge_forms' => $pledgeForms->items(),
                    'pagination' => [
                        'current_page' => $pledgeForms->currentPage(),
                        'last_page' => $pledgeForms->lastPage(),
                        'per_page' => $pledgeForms->perPage(),
                        'total' => $pledgeForms->total(),
                        'from' => $pledgeForms->firstItem(),
                        'to' => $pledgeForms->lastItem(),
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation rules
            $rules = [
                'name_of_nurse' => 'required|string|max:255',
                'name_of_patient' => 'required|string|max:255',
                'identity_number_of_patient' => 'required|string|max:255',
                'phone_of_patient' => 'nullable|string|max:255',
                'professional_license_number' => 'nullable|string|max:255',
                'pledge_text' => 'nullable|string',
                'date_of_pledge' => 'nullable|date',
                'signature_one' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'signature_two' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'signature_three' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'signature_four' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'type' => 'required|in:pledge_form,authorization_form',
                'room_id' => 'required|exists:rooms,id',
            ];

            // Add authorization form specific validation rules
            if ($request->type === 'authorization_form') {
                $authorizationRules = [
                    'place' => 'nullable|string|max:255',
                    'date_of_birth' => 'nullable|date',
                    'parent_of_patient' => 'nullable|string|max:255',
                    'identity_number_for_parent_of_patient' => 'nullable|string|max:255',
                    'phone_for_parent_of_patient' => 'nullable|string|max:255',
                    'kinship' => 'nullable|string|max:255',
                    'full_name_of_commissioner' => 'nullable|string|max:255',
                ];
                $rules = array_merge($rules, $authorizationRules);
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            // Prepare data for storage
            $data = $request->except(['signature_one', 'signature_two', 'signature_three', 'signature_four']);

            // Handle signature uploads
            $signatureFields = ['signature_one', 'signature_two', 'signature_three', 'signature_four'];
            
            foreach ($signatureFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $path = uploadImage('assets/admin/uploads', $file);
                    $data[$field] = $path;
                }
            }

            // Create the pledge form
            $pledgeForm = PledgeForm::create($data);
            
            // Load the room relationship
            $pledgeForm->load('room');

            return $this->success_response(
                __('messages.pledge_form_created_successfully'),
                ['pledge_form' => $pledgeForm]
            );

        } catch (\Exception $e) {

            return $this->error_response(
                __('messages.error_creating_pledge_form'),
                ['error' => $e->getMessage()]
            );
        }
    }
}