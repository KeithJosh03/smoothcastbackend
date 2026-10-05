<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function resolveCart(Request $request)
    {
        // Try to get authenticated user via Sanctum (if token is passed)
        $userId = Auth::guard('sanctum')->id();
        $sessionId = $request->header('X-Session-ID') ?? $request->input('session_id');

        if ($userId) {
            // User is logged in
            $cart = Cart::firstOrCreate(['user_id' => $userId]);
            
            // If there's a guest cart, merge it
            if ($sessionId) {
                $guestCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
                if ($guestCart && $guestCart->cart_id !== $cart->cart_id) {
                    foreach ($guestCart->items as $item) {
                        $existing = $cart->items()
                            ->where('product_id', $item->product_id)
                            ->where('sku_id', $item->sku_id)
                            ->first();
                            
                        if ($existing) {
                            $existing->quantity += $item->quantity;
                            $existing->save();
                            $item->delete();
                        } else {
                            $item->cart_id = $cart->cart_id;
                            $item->save();
                        }
                    }
                    $guestCart->delete();
                }
            }
            return $cart;
        }

        // Guest user
        if (!$sessionId) {
            return null; // The frontend should always send a session ID for guests
        }

        return Cart::firstOrCreate(['session_id' => $sessionId, 'user_id' => null]);
    }

    public function getCart(Request $request)
    {
        $cart = $this->resolveCart($request);
        
        if (!$cart) {
            return response()->json([
                'status' => true,
                'cart' => null
            ]);
        }

        // Eager load relations for the cart items
        $cart->load(['items.product.images', 'items.sku']);

        return response()->json([
            'status' => true,
            'cart' => $cart
        ]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'sku_id' => 'nullable|exists:product_skus,sku_id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = $this->resolveCart($request);
        
        if (!$cart) {
            return response()->json(['status' => false, 'message' => 'Session ID is required for guest carts.'], 400);
        }

        // Check if item already exists
        $cartItem = $cart->items()
            ->where('product_id', $validated['product_id'])
            ->where('sku_id', $validated['sku_id'])
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $validated['product_id'],
                'sku_id' => $validated['sku_id'],
                'quantity' => $validated['quantity']
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Item added to cart successfully.'
        ]);
    }

    public function updateQuantity(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::findOrFail($itemId);
        
        // Security check: ensure item belongs to user's cart
        $cart = $this->resolveCart($request);
        if (!$cart || $cartItem->cart_id !== $cart->cart_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $cartItem->quantity = $validated['quantity'];
        $cartItem->save();

        return response()->json([
            'status' => true,
            'message' => 'Quantity updated.'
        ]);
    }

    public function removeItem(Request $request, $itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        
        // Security check: ensure item belongs to user's cart
        $cart = $this->resolveCart($request);
        if (!$cart || $cartItem->cart_id !== $cart->cart_id) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item removed from cart.'
        ]);
    }
}
