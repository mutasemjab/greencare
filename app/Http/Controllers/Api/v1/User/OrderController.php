<?php
namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Traits\Responses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\FirestoreMessageService;

class OrderController extends Controller
{
    use Responses;

     public function index(Request $request)
    {
        $orders = Order::with([
            'orderProducts',
            'orderProducts.product',
            'orderProducts.product.images',
        ])->where('user_id', $request->user()->id)->get();

        return $this->success_response('Orders retrieved successfully', $orders);
    }




   public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'nullable|exists:user_addresses,id',
            'payment_type' => 'required|in:cash,card',
            'coupon_code' => 'nullable|string',
            'code' => 'nullable|exists:rooms,code', 
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();
            
            // Get cart items with product relationships
            $cartItems = Cart::with(['product'])
                            ->where('user_id', $user->id)
                            ->where('status', 1)
                            ->get();

            if ($cartItems->isEmpty()) {
                return $this->error_response('Cart is empty', []);
            }

            // Get delivery address and fee
            $deliveryAddress = \App\Models\UserAddress::find($request->address_id);
            $deliveryFee = $deliveryAddress->delivery->price ?? 0;

            $totalTax = 0;
            $totalBeforeTax = 0;
            $totalDiscount = 0;
            $orderProducts = [];

            // Process each cart item
            foreach ($cartItems as $item) {
                $product = $item->product;

                // Calculate base price (product price adjustment if any)
                $basePrice = $product->price_after_discount ?? $product->price;
            

                // Calculate discount value per unit
                $originalPrice = $product->price;
                
                $discountValue = $originalPrice - $basePrice;

                // Calculate subtotal before tax
                $productSubtotal = $basePrice * $item->quantity;

                // Calculate tax
                $taxRate = $product->tax ?? 10; // default 10%
                $taxValue = $productSubtotal * ($taxRate / 100);

                // Prepare order product data
                $orderProducts[] = [
                    'order_id' => null, // Will be set after order creation
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $basePrice,
                    'total_price_before_tax' => $productSubtotal,
                    'tax_percentage' => $taxRate,
                    'tax_value' => $taxValue,
                    'total_price_after_tax' => $productSubtotal + $taxValue,
                    'discount_percentage' => $product->discount_percentage ?? 0,
                    'discount_value' => $discountValue * $item->quantity,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Add to totals
                $totalBeforeTax += $productSubtotal;
                $totalTax += $taxValue;
                $totalDiscount += $discountValue * $item->quantity;
            }

            // Handle coupon discount
            $couponDiscount = 0;
            $couponId = null;

            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)
                    ->whereDate('expired_at', '>=', today())
                    ->where('status', 1) // Assuming active status
                    ->first();

                if ($coupon) {
                    // Check if user already used this coupon
                    $alreadyUsed = DB::table('user_coupons')
                        ->where('user_id', $user->id)
                        ->where('coupon_id', $coupon->id)
                        ->exists();

                    // Apply coupon if not used and minimum total is met
                    if (!$alreadyUsed && $totalBeforeTax >= $coupon->minimum_total) {
                        if ($coupon->type === 'percentage') {
                            $couponDiscount = ($totalBeforeTax * $coupon->amount) / 100;
                            // Apply max discount limit if exists
                            if ($coupon->max_discount && $couponDiscount > $coupon->max_discount) {
                                $couponDiscount = $coupon->max_discount;
                            }
                        } else {
                            // Fixed amount coupon
                            $couponDiscount = $coupon->amount;
                        }

                        $couponId = $coupon->id;

                        // Record coupon usage
                        DB::table('user_coupons')->insert([
                            'user_id' => $user->id,
                            'coupon_id' => $coupon->id,
                            'used_at' => now(),
                            'order_total' => $totalBeforeTax,
                            'discount_amount' => $couponDiscount,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Coupon validation failed
                        $errorMessage = $alreadyUsed ? 'Coupon already used' : 'Order total does not meet minimum requirement';
                        return $this->error_response($errorMessage, []);
                    }
                } else {
                    return $this->error_response('Invalid or expired coupon code', []);
                }
            }

            // Handle room discount
            $roomDiscount = 0;
            $room = null;

            if ($request->code) {
                $room = \App\Models\Room::where('code', $request->code)->first();
                
                if ($room && $room->discount > 0) {
                    // Calculate room discount as percentage of total before tax
                    $roomDiscount = ($totalBeforeTax * $room->discount) / 100;
                }
            }

            // Calculate final total
            $totalFinal = $totalBeforeTax + $totalTax + $deliveryFee - $couponDiscount - $roomDiscount;

            // Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $request->address_id,
                'total_prices' => $totalFinal,
                'total_taxes' => $totalTax,
                'delivery_fee' => $deliveryFee,
                'total_discounts' => $totalDiscount,
                'coupon_discount' => $couponDiscount,
                'payment_type' => $request->payment_type,
                'payment_status' => 2, // Unpaid
                'order_status' => 1, // Pending
                'date' => now(),
                'note' => $request->note ?? null
            ]);

            // Set order number (using order ID)
            $order->number = $order->id;
            $order->save();

            // Update order_id in orderProducts array
            foreach ($orderProducts as &$orderProduct) {
                $orderProduct['order_id'] = $order->id;
            }

            // Insert order products
            OrderProduct::insert($orderProducts);

            // Mark cart items as ordered (status = 2)
            Cart::where('user_id', $user->id)
                ->where('status', 1)
                ->update(['status' => 2]);

            // Load order with relationships for response
            $order->load([
                'orderProducts',
                'orderProducts.product',
                'orderProducts.product.images',
                'address'
            ]);

            DB::commit();

            // ============================================
            // Send message to Firestore room
            // ============================================
            try {
                if ($room) {
                    $firestoreMessageService = app(FirestoreMessageService::class);
                    
                    $orderMessageData = [
                        'order_id' => $order->id,
                        'order_number' => $order->number,
                        'total' => $totalFinal,
                        'currency' => 'JD', // Or get from config
                        'items_count' => $cartItems->sum('quantity'),
                        'payment_type' => $order->payment_type,
                        'delivery_fee' => $deliveryFee,
                        'discount' => $totalDiscount + $couponDiscount + $roomDiscount,
                        'status' => 'pending',
                        'sender_avatar' => $user->photo ?? '',
                    ];
                    
                    $firestoreMessageService->sendOrderMessage(
                        $room->id,
                        $orderMessageData,
                        $user->id,
                        $user->name
                    );
                }
            } catch (\Exception $e) {
                // Log error but don't fail the order creation
                \Log::error('Failed to send order message to Firestore: ' . $e->getMessage());
            }
            // ============================================

            return $this->success_response('Order created successfully', [
                'order' => $order,
                'order_summary' => [
                    'subtotal' => $totalBeforeTax,
                    'tax_total' => $totalTax,
                    'delivery_fee' => $deliveryFee,
                    'coupon_discount' => $couponDiscount,
                    'room_discount' => $roomDiscount,
                    'product_discount' => $totalDiscount,
                    'final_total' => $totalFinal,
                    'items_count' => $cartItems->sum('quantity')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Order creation failed: ' . $e->getMessage());
            return $this->error_response('Failed to create order', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
        }
    }



    public function details($id)
    {
        $order = Order::with('orderProducts','orderProducts.product','orderProducts.product.images')->find($id);

        if (!$order) {
            return $this->error_response('Order not found', []);
        }

        return $this->success_response('Order details', $order);
    }

    public function cancelOrder($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->error_response('Order not found', []);
        }

        $order->order_status = 5; // Cancelled
        $order->save();

        return $this->success_response('Order cancelled successfully', $order);
    }
   
}