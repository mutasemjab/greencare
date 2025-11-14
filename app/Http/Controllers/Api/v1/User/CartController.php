<?php
namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    use Responses;

    public function index(Request $request)
    {
        $user = auth()->user();
        
        if(!$user){
             return $this->error_response('Unauthenticated', [], 401);
        }
        $cart = Cart::with([
            'product', 
            'product.images', 
        ])->where('user_id',  $user->id)
          ->where('status', 1)
          ->get();

        $cartData = [];
        foreach ($cart as $item) {
            $cartData[] = [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total_price_product' => $item->total_price_product,
                'product' => [
                    'id' => $item->product->id,
                    'name_en' => $item->product->name_en,
                    'name_ar' => $item->product->name_ar,
                    'images' => $item->product->images,
                ],
                'created_at' => $item->created_at->toISOString(),
            ];
        }

        return $this->success_response('Cart retrieved successfully', $cartData);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
        ]);

        $user = auth()->user();
        
        if(!$user){
             return $this->error_response('Unauthenticated', [], 401);
        }

        $product = Product::find($request->product_id);
        $price = $product->price_after_discount ?? $product->price;

        

        $userId =  $user->id;

        // Check if the same product with same  already exists in cart
        $cart = Cart::where('user_id', $userId)
                    ->where('product_id', $request->product_id)
                    ->where('status', 1)
                    ->first();

        if ($cart) {
            // Update quantity and total
            $cart->quantity += $request->quantity;
            $cart->total_price_product = $cart->price * $cart->quantity;
            $cart->save();

            return $this->success_response('Cart updated with new quantity', $cart);
        } else {
            // Create new cart item
            $cart = Cart::create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'quantity' => $request->quantity,
                'price' => $price,
                'total_price_product' => $price * $request->quantity,
                'status' => 1
            ]);

            return $this->success_response('Product added to cart', $cart);
        }
    }

    public function delete($id)
    {
        $cart = Cart::find($id);

        if (!$cart) {
            return $this->error_response('Cart item not found', []);
        }

        $cart->delete();

        return $this->success_response('Cart item deleted', []);
    }
   
}