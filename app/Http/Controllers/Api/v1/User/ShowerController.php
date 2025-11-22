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

            if ($request->code_patient) {
                dispatch(function () use ($request, $shower, $userId, $finalPrice, $discountInfo) {
                    try {
                        // Find room by code
                        $room = \App\Models\Room::where('code', $request->code_patient)->first();
                        
                        if (!$room) {
                            return;
                        }
                        
                        // Check if user is in this room
                        $userInRoom = DB::table('room_users')
                            ->where('room_id', $room->id)
                            ->where('user_id', $userId)
                            ->where('role', 'patient')
                            ->exists();
                        
                        if (!$userInRoom) {
                            return;
                        }
                        
                        $user = \App\Models\User::find($userId);
                        
                        if (!$user) {
                            return;
                        }
                        
                        $projectId = config('firebase.project_id');
                        $baseUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents";
                        
                        // Generate 20 character random ID
                        $messageId = \Illuminate\Support\Str::random(20);
                        
                        // Format shower appointment message
                        $messageText = "🚿 New Shower Appointment\n\n";
                        $messageText .= "Patient Code: {$request->code_patient}\n";
                        $messageText .= "Date: " . \Carbon\Carbon::parse($shower->date_of_shower)->format('M d, Y') . "\n";
                        
                        if ($shower->time_of_shower) {
                            $messageText .= "Time: " . \Carbon\Carbon::parse($shower->time_of_shower)->format('h:i A') . "\n";
                        }
                        
                        $messageText .= "Price: $" . number_format($finalPrice, 2) . "\n";
                        
                        if ($discountInfo) {
                            $messageText .= "Original Price: $" . number_format($discountInfo['original_price'], 2) . "\n";
                            $messageText .= "Discount: " . $discountInfo['discount_percentage'] . "% (-$" . number_format($discountInfo['discount_amount'], 2) . ")\n";
                        }
                        
                        if ($shower->note) {
                            $messageText .= "\nNote: " . $shower->note;
                        }
                        
                        // Prepare message data
                        $messageData = [
                            'fields' => [
                                'created_at' => ['timestampValue' => now()->toIso8601String()],
                                'id' => ['stringValue' => $messageId],
                                'is_delivered' => ['booleanValue' => false],
                                'is_read' => ['booleanValue' => false],
                                'media_url' => ['stringValue' => ''],
                                'reply_to' => ['stringValue' => ''],
                                'sender_avatar' => ['stringValue' => $user->photo ?? 'null'],
                                'sender_id' => ['integerValue' => (string)$user->id],
                                'sender_name' => ['stringValue' => $user->name],
                                'text' => ['stringValue' => $messageText],
                                'type' => ['stringValue' => 'shower'],
                            ]
                        ];
                        
                        // Send to Firestore messages subcollection
                        $response = \Illuminate\Support\Facades\Http::timeout(10)->patch(
                            "{$baseUrl}/rooms/room_{$room->id}/messages/{$messageId}",
                            $messageData
                        );
                        
                        if ($response->successful()) {
                            \Log::info("Shower appointment message sent to room: {$room->code} (ID: {$room->id})");
                            
                            // Update room's last_message
                            $roomUpdate = [
                                'fields' => [
                                    'last_message' => ['stringValue' => $messageText],
                                    'last_message_at' => ['timestampValue' => now()->toIso8601String()],
                                ]
                            ];
                            
                            \Illuminate\Support\Facades\Http::timeout(10)->patch(
                                "{$baseUrl}/rooms/room_{$room->id}?updateMask.fieldPaths=last_message&updateMask.fieldPaths=last_message_at",
                                $roomUpdate
                            );
                        } else {
                            \Log::error('Failed to send shower appointment message', [
                                'room_code' => $room->code,
                                'status' => $response->status(),
                                'body' => $response->body()
                            ]);
                        }
                        
                    } catch (\Exception $e) {
                        \Log::error('Failed to send shower appointment message: ' . $e->getMessage());
                    }
                })->afterResponse();
            }

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