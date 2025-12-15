<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\TransferPatient;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransferPatientController extends Controller
{
    use Responses;
    /**
     * Store a newly created transfer request
     */
    public function store(Request $request)
    {
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'date_of_transfer' => 'required|date|after_or_equal:today',
                'time_of_transfer' => 'nullable|date_format:H:i',
                'note' => 'nullable|string|max:1000',
                'from_address' => 'required|string|max:500',
                'from_lat' => 'nullable|numeric|between:-90,90',
                'from_lng' => 'nullable|numeric|between:-180,180',
                'from_place' => 'required|integer|in:1,2',
                'to_address' => 'required|string|max:500',
                'to_lat' => 'nullable|numeric|between:-90,90',
                'to_lng' => 'nullable|numeric|between:-180,180',
                'to_place' => 'required|integer|in:1,2',
                'user_id' => 'nullable|integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    $validator->errors()
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

            DB::beginTransaction();

            // Create the transfer request
            $transfer = TransferPatient::create([
                'user_id' => $userId,
                'date_of_transfer' => $request->date_of_transfer,
                'time_of_transfer' => $request->time_of_transfer,
                'note' => $request->note,
                'from_address' => $request->from_address,
                'from_lat' => $request->from_lat,
                'from_lng' => $request->from_lng,
                'from_place' => $request->from_place,
                'to_address' => $request->to_address,
                'to_lat' => $request->to_lat,
                'to_lng' => $request->to_lng,
                'to_place' => $request->to_place,
            ]);

            DB::commit();

            // Load user relationship for response
            $transfer->load('user');

            // Format response data
            $transferData = [
                'id' => $transfer->id,
                'date_of_transfer' => $transfer->date_of_transfer->format('Y-m-d'),
                'time_of_transfer' => $transfer->time_of_transfer ? $transfer->time_of_transfer->format('H:i') : null,
                'formatted_transfer' => $transfer->formatted_transfer,
                'note' => $transfer->note,
                'from_address' => $transfer->from_address,
                'from_lat' => $transfer->from_lat,
                'from_lng' => $transfer->from_lng,
                'from_place' => $transfer->from_place,
                'from_place_text' => $transfer->from_place_text,
                'to_address' => $transfer->to_address,
                'to_lat' => $transfer->to_lat,
                'to_lng' => $transfer->to_lng,
                'to_place' => $transfer->to_place,
                'to_place_text' => $transfer->to_place_text,
                'user' => [
                    'id' => $transfer->user->id,
                    'name' => $transfer->user->name,
                    'email' => $transfer->user->email,
                ],
                'created_at' => $transfer->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $transfer->updated_at->format('Y-m-d H:i:s'),
            ];

            return $this->success_response(
                __('messages.transfer_created_successfully'),
                $transferData
            );

        } catch (\Exception $e) {
            DB::rollback();
            return $this->error_response(
                __('messages.error_creating_transfer'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Get user's transfer history
     */
    public function index(Request $request)
    {
        try {
            $userId = $request->user_id ?? Auth::id();
            
            if (!$userId) {
                return $this->error_response(
                    __('messages.user_required'),
                    []
                );
            }

            $query = TransferPatient::where('user_id', $userId)
                ->with('user');

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->where('date_of_transfer', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('date_of_transfer', '<=', $request->date_to);
            }

            $transfers = $query->latest('date_of_transfer')
                ->latest('time_of_transfer')
                ->paginate(20);

            $data = $transfers->map(function($transfer) {
                return [
                    'id' => $transfer->id,
                    'date_of_transfer' => $transfer->date_of_transfer->format('Y-m-d'),
                    'time_of_transfer' => $transfer->time_of_transfer ? $transfer->time_of_transfer->format('H:i') : null,
                    'formatted_transfer' => $transfer->formatted_transfer,
                    'note' => $transfer->note,
                    'from_address' => $transfer->from_address,
                    'from_place_text' => $transfer->from_place_text,
                    'to_address' => $transfer->to_address,
                    'to_place_text' => $transfer->to_place_text,
                    'created_at' => $transfer->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return $this->success_response(
                __('messages.transfers_retrieved_successfully'),
                [
                    'transfers' => $data,
                    'pagination' => [
                        'total' => $transfers->total(),
                        'per_page' => $transfers->perPage(),
                        'current_page' => $transfers->currentPage(),
                        'last_page' => $transfers->lastPage(),
                    ]
                ]
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_retrieving_transfers'),
                ['error' => $e->getMessage()]
            );
        }
    }


}