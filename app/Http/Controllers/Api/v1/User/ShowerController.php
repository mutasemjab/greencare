<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Shower;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShowerController extends Controller
{
    use Responses;
    /**
     * Store a new shower appointment.
     */
    public function store(Request $request)
    {
        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'code_patient' => 'nullable|string|max:255',
                'date_of_shower' => 'required|date|after_or_equal:today',
                'time_of_shower' => 'nullable|date_format:H:i',
                'note' => 'nullable|string|max:1000',
                'price' => 'nullable|numeric|min:0',
                'user_id' => 'nullable|integer|exists:users,id'
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

            // Calculate price with discount logic
            $finalPrice = $request->price ?? 0;
            $discountInfo = null;
            $appliedDiscount = 0;

            // If price is not provided, get from settings
            if (!$request->has('price')) {
                $defaultPrice = DB::table('settings')
                    ->where('key', 'amount_of_shower_patient')
                    ->value('value');
                
                $finalPrice = $defaultPrice ?? 0;
            }

            // Check if patient code exists and if user is in that room
            if ($request->code_patient) {
                $room = DB::table('rooms')
                    ->where('code', $request->code_patient)
                    ->first();

                if ($room) {
                    // Check if the user is a patient in this room
                    $userInRoom = DB::table('room_users')
                        ->where('room_id', $room->id)
                        ->where('user_id', $userId)
                        ->where('role', 'patient')
                        ->exists();

                    if ($userInRoom && $room->discount > 0) {
                        // Apply discount
                        $appliedDiscount = $room->discount;
                        $discountAmount = ($finalPrice * $appliedDiscount) / 100;
                        $finalPrice = $finalPrice - $discountAmount;

                        $discountInfo = [
                            'room_title' => $room->title,
                            'discount_percentage' => $room->discount,
                            'discount_amount' => round($discountAmount, 2),
                            'original_price' => $request->price ?? DB::table('settings')->where('key', 'amount_of_shower_patient')->value('value') ?? 0,
                            'discounted_price' => round($finalPrice, 2)
                        ];
                    }
                }
            }

            // Create the shower appointment
            $shower = Shower::create([
                'code_patient' => $request->code_patient,
                'date_of_shower' => $request->date_of_shower,
                'time_of_shower' => $request->time_of_shower,
                'note' => $request->note,
                'price' => round($finalPrice, 2),
                'user_id' => $userId
            ]);

            // Load user relationship for response
            $shower->load('user');

            // Format response data
            $showerData = [
                'id' => $shower->id,
                'code_patient' => $shower->code_patient,
                'date_of_shower' => $shower->date_of_shower->format('Y-m-d'),
                'time_of_shower' => $shower->time_of_shower ? $shower->time_of_shower->format('H:i') : null,
                'formatted_shower' => $shower->formatted_shower,
                'note' => $shower->note,
                'price' => $shower->price,
                'discount_applied' => $appliedDiscount > 0,
                'discount_info' => $discountInfo,
                'user' => [
                    'id' => $shower->user->id,
                    'name' => $shower->user->name,
                    'email' => $shower->user->email,
                ],
                'created_at' => $shower->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $shower->updated_at->format('Y-m-d H:i:s'),
            ];

            return $this->success_response(
                __('messages.shower_appointment_created_successfully'),
                $showerData
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_creating_shower_appointment'),
                ['error' => $e->getMessage()]
            );
        }
    }
    

    

    
}