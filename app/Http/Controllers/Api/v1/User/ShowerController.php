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
                'user_id' => 'nullable|integer|exists:users,id',
                'card_number' => 'nullable|string|exists:card_numbers,number' // إضافة validation للبطاقة
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
            $cardNumberId = null;
            $paymentMethod = 'cash'; // طريقة الدفع الافتراضية

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

            // التحقق من البطاقة إذا تم إرسالها
            if ($request->card_number) {
                // جلب معلومات البطاقة
                $cardNumber = DB::table('card_numbers')
                    ->join('cards', 'card_numbers.card_id', '=', 'cards.id')
                    ->where('card_numbers.number', $request->card_number)
                    ->select(
                        'card_numbers.id as card_number_id',
                        'card_numbers.activate',
                        'card_numbers.status',
                        'card_numbers.sell',
                        'card_numbers.assigned_user_id',
                        'cards.price as card_price',
                        'cards.name as card_name'
                    )
                    ->first();

                if (!$cardNumber) {
                    return $this->error_response(
                        __('messages.card_not_found'),
                        []
                    );
                }

                // التحقق من أن البطاقة نشطة
                if ($cardNumber->activate != 1) {
                    return $this->error_response(
                        __('messages.card_not_active'),
                        []
                    );
                }

                // التحقق من أن البطاقة لم تُستخدم بعد
                if ($cardNumber->status == 1) {
                    return $this->error_response(
                        __('messages.card_already_used'),
                        []
                    );
                }

                // التحقق من أن البطاقة تم بيعها
                if ($cardNumber->sell != 1) {
                    return $this->error_response(
                        __('messages.card_not_sold_yet'),
                        []
                    );
                }


                // الشرط الأساسي: التحقق من أن سعر البطاقة يساوي سعر الاستحمام
                if (round($cardNumber->card_price, 2) != round($finalPrice, 2)) {
                    return $this->error_response(
                        __('messages.card_price_mismatch'),
                        [
                            'card_price' => round($cardNumber->card_price, 2),
                            'shower_price' => round($finalPrice, 2),
                            'message' => 'سعر البطاقة (' . round($cardNumber->card_price, 2) . ') لا يساوي سعر الاستحمام (' . round($finalPrice, 2) . ')'
                        ]
                    );
                }

                // إذا كانت جميع الشروط صحيحة، استخدم البطاقة
                $cardNumberId = $cardNumber->card_number_id;
                $paymentMethod = 'card';
            }

            // Create the shower appointment
            $shower = Shower::create([
                'code_patient' => $request->code_patient,
                'date_of_shower' => $request->date_of_shower,
                'time_of_shower' => $request->time_of_shower,
                'note' => $request->note,
                'price' => round($finalPrice, 2),
                'user_id' => $userId,
                'card_number_id' => $cardNumberId // حفظ معرف البطاقة
            ]);

            // إذا تم استخدام بطاقة، قم بتحديث حالتها
            if ($cardNumberId) {
                // تحديث حالة البطاقة إلى "مستخدمة"
                DB::table('card_numbers')
                    ->where('id', $cardNumberId)
                    ->update([
                        'status' => 1, // used
                        'updated_at' => now()
                    ]);

                // تسجيل استخدام البطاقة في جدول card_usages
                DB::table('card_usages')->insert([
                    'user_id' => $userId,
                    'card_number_id' => $cardNumberId,
                    'used_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

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
                'payment_method' => $paymentMethod,
                'discount_applied' => $appliedDiscount > 0,
                'discount_info' => $discountInfo,
                'card_used' => $cardNumberId ? true : false,
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