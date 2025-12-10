<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shower;
use App\Models\User;
use App\Models\Room;
use App\Models\CardNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShowerController extends Controller
{
    /**
     * Display a listing of shower appointments
     */
    public function index(Request $request)
    {
        $query = Shower::with(['user', 'cardNumber.card']);

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('date_of_shower', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_of_shower', '<=', $request->date_to);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by patient code
        if ($request->filled('code_patient')) {
            $query->where('code_patient', 'like', '%' . $request->code_patient . '%');
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            if ($request->payment_method == 'card') {
                $query->whereNotNull('card_number_id');
            } elseif ($request->payment_method == 'cash') {
                $query->whereNull('card_number_id');
            }
        }

        $showers = $query->orderBy('date_of_shower', 'desc')
                        ->orderBy('time_of_shower', 'desc')
                        ->paginate(15);

        $users = User::orderBy('name')->where('user_type','patient')->get();
        
        // Statistics
        $stats = [
            'total' => Shower::count(),
            'today' => Shower::whereDate('date_of_shower', today())->count(),
            'this_month' => Shower::whereMonth('date_of_shower', now()->month)
                                  ->whereYear('date_of_shower', now()->year)
                                  ->count(),
            'total_revenue' => Shower::sum('price'),
            'card_payments' => Shower::whereNotNull('card_number_id')->count(),
            'cash_payments' => Shower::whereNull('card_number_id')->count(),
        ];

        return view('admin.showers.index', compact('showers', 'users', 'stats'));
    }

    /**
     * Show the form for creating a new shower appointment
     */
    public function create()
    {
        $users = User::orderBy('name')->where('user_type','patient')->get();
        $rooms = Room::orderBy('title')->get();
        
        // Get available cards (active, not used, sold)
        $availableCards = CardNumber::with('card')
            ->where('activate', 1)
            ->where('status', 2)
            ->where('sell', 2)
            ->orderBy('number')
            ->get();

        $defaultPrice = DB::table('settings')
            ->where('key', 'amount_of_shower_patient')
            ->value('value') ?? 0;

        return view('admin.showers.create', compact('users', 'rooms', 'availableCards', 'defaultPrice'));
    }

    /**
     * Store a newly created shower appointment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code_patient' => 'nullable|string|max:255',
            'date_of_shower' => 'required|date',
            'time_of_shower' => 'nullable|date_format:H:i',
            'note' => 'nullable|string|max:1000',
            'price' => 'nullable|numeric|min:0',
            'user_id' => 'required|integer|exists:users,id',
            'card_number_id' => 'nullable|exists:card_numbers,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $userId = $request->user_id;
            $finalPrice = $request->price ?? 0;
            $discountInfo = null;
            $appliedDiscount = 0;
            $cardNumberId = $request->card_number_id;

            // If price is not provided, get from settings
            if (!$request->has('price')) {
                $defaultPrice = DB::table('settings')
                    ->where('key', 'amount_of_shower_patient')
                    ->value('value');
                
                $finalPrice = $defaultPrice ?? 0;
            }

            // Check if patient code exists and apply discount
            if ($request->code_patient) {
                $room = Room::where('code', $request->code_patient)->first();

                if ($room) {
                    $userInRoom = DB::table('room_users')
                        ->where('room_id', $room->id)
                        ->where('user_id', $userId)
                        ->where('role', 'patient')
                        ->exists();

                    if ($userInRoom && $room->discount > 0) {
                        $appliedDiscount = $room->discount;
                        $discountAmount = ($finalPrice * $appliedDiscount) / 100;
                        $finalPrice = $finalPrice - $discountAmount;
                    }
                }
            }

            // Verify card if provided
            if ($cardNumberId) {
                $cardNumber = CardNumber::with('card')->find($cardNumberId);

                if (!$cardNumber) {
                    return redirect()->back()
                        ->with('error', 'البطاقة غير موجودة')
                        ->withInput();
                }

                if ($cardNumber->activate != 1) {
                    return redirect()->back()
                        ->with('error', 'البطاقة غير نشطة')
                        ->withInput();
                }

                if ($cardNumber->status == 1) {
                    return redirect()->back()
                        ->with('error', 'البطاقة مستخدمة من قبل')
                        ->withInput();
                }

                if ($cardNumber->sell != 1) {
                    return redirect()->back()
                        ->with('error', 'البطاقة لم يتم بيعها بعد')
                        ->withInput();
                }

                // Verify price match
                if (round($cardNumber->card->price, 2) != round($finalPrice, 2)) {
                    return redirect()->back()
                        ->with('error', 'سعر البطاقة (' . round($cardNumber->card->price, 2) . ') لا يساوي سعر الاستحمام (' . round($finalPrice, 2) . ')')
                        ->withInput();
                }
            }

            // Create shower appointment
            $shower = Shower::create([
                'code_patient' => $request->code_patient,
                'date_of_shower' => $request->date_of_shower,
                'time_of_shower' => $request->time_of_shower,
                'note' => $request->note,
                'price' => round($finalPrice, 2),
                'user_id' => $userId,
                'card_number_id' => $cardNumberId
            ]);

            // Update card if used
            if ($cardNumberId) {
                CardNumber::where('id', $cardNumberId)->update([
                    'status' => 1,
                    'updated_at' => now()
                ]);

                DB::table('card_usages')->insert([
                    'user_id' => $userId,
                    'card_number_id' => $cardNumberId,
                    'used_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('admin.showers.index')
                ->with('success', 'تم إنشاء موعد الاستحمام بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified shower appointment
     */
    public function show($id)
    {
        $shower = Shower::with(['user', 'cardNumber.card'])->findOrFail($id);
        
        // Get discount info if applicable
        $discountInfo = null;
        if ($shower->code_patient) {
            $room = Room::where('code', $shower->code_patient)->first();
            if ($room && $room->discount > 0) {
                $discountInfo = [
                    'room' => $room,
                    'discount_percentage' => $room->discount
                ];
            }
        }

        return view('admin.showers.show', compact('shower', 'discountInfo'));
    }

    /**
     * Show the form for editing the specified shower appointment
     */
    public function edit($id)
    {
        $shower = Shower::findOrFail($id);
        $users = User::orderBy('name')->where('user_type','patient')->get();
        $rooms = Room::orderBy('title')->get();
        
        // Get available cards
        $availableCards = CardNumber::with('card')
            ->where(function($query) use ($shower) {
                $query->where(function($q) {
                    $q->where('activate', 1)
                      ->where('status', 2)
                      ->where('sell', 2);
                })
                ->orWhere('id', $shower->card_number_id);
            })
            ->orderBy('number')
            ->get();

        $defaultPrice = DB::table('settings')
            ->where('key', 'amount_of_shower_patient')
            ->value('value') ?? 0;

        return view('admin.showers.edit', compact('shower', 'users', 'rooms', 'availableCards', 'defaultPrice'));
    }

    /**
     * Update the specified shower appointment
     */
    public function update(Request $request, $id)
    {
        $shower = Shower::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code_patient' => 'nullable|string|max:255',
            'date_of_shower' => 'required|date',
            'time_of_shower' => 'nullable|date_format:H:i',
            'note' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'user_id' => 'required|integer|exists:users,id',
            'card_number_id' => 'nullable|exists:card_numbers,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $oldCardNumberId = $shower->card_number_id;
            $newCardNumberId = $request->card_number_id;

            // Verify new card if provided and different from old
            if ($newCardNumberId && $newCardNumberId != $oldCardNumberId) {
                $cardNumber = CardNumber::with('card')->find($newCardNumberId);

                if (!$cardNumber) {
                    return redirect()->back()
                        ->with('error', 'البطاقة غير موجودة')
                        ->withInput();
                }

                if ($cardNumber->activate != 1 || $cardNumber->status == 1 || $cardNumber->sell != 1) {
                    return redirect()->back()
                        ->with('error', 'البطاقة غير صالحة للاستخدام')
                        ->withInput();
                }

                if (round($cardNumber->card->price, 2) != round($request->price, 2)) {
                    return redirect()->back()
                        ->with('error', 'سعر البطاقة لا يتطابق مع سعر الاستحمام')
                        ->withInput();
                }
            }

            // Update shower
            $shower->update([
                'code_patient' => $request->code_patient,
                'date_of_shower' => $request->date_of_shower,
                'time_of_shower' => $request->time_of_shower,
                'note' => $request->note,
                'price' => round($request->price, 2),
                'user_id' => $request->user_id,
                'card_number_id' => $newCardNumberId
            ]);

            // Handle card changes
            if ($oldCardNumberId != $newCardNumberId) {
                // Restore old card if exists
                if ($oldCardNumberId) {
                    CardNumber::where('id', $oldCardNumberId)->update(['status' => 2]);
                    DB::table('card_usages')->where('card_number_id', $oldCardNumberId)->delete();
                }

                // Mark new card as used
                if ($newCardNumberId) {
                    CardNumber::where('id', $newCardNumberId)->update(['status' => 1]);
                    DB::table('card_usages')->insert([
                        'user_id' => $request->user_id,
                        'card_number_id' => $newCardNumberId,
                        'used_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.showers.index')
                ->with('success', 'تم تحديث موعد الاستحمام بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified shower appointment
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $shower = Shower::findOrFail($id);
            $cardNumberId = $shower->card_number_id;

            // Restore card if used
            if ($cardNumberId) {
                CardNumber::where('id', $cardNumberId)->update(['status' => 2]);
                DB::table('card_usages')->where('card_number_id', $cardNumberId)->delete();
            }

            $shower->delete();

            DB::commit();

            return redirect()->route('admin.showers.index')
                ->with('success', 'تم حذف موعد الاستحمام بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Get card price via AJAX
     */
    public function getCardPrice($cardNumberId)
    {
        $cardNumber = CardNumber::with('card')->find($cardNumberId);
        
        if (!$cardNumber) {
            return response()->json(['error' => 'البطاقة غير موجودة'], 404);
        }

        return response()->json([
            'price' => $cardNumber->card->price,
            'card_name' => $cardNumber->card->name,
            'card_number' => $cardNumber->number,
            'status' => $cardNumber->status,
            'activate' => $cardNumber->activate,
            'sell' => $cardNumber->sell
        ]);
    }
}