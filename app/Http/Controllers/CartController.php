<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the cart
     */
    public function index(): View
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.variant.product.media']);

        return view('cart.index', compact('cart'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($request->variant_id);
        
        // Check stock
        if ($variant->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Không đủ hàng trong kho. Còn lại: ' . $variant->stock . ' sản phẩm.'
            ], 400);
        }

        $cart = $this->getOrCreateCart();
        
        // Check if item already exists in cart
        $existingItem = $cart->items()->where('product_variant_id', $variant->id)->first();
        
        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem->quantity + $request->quantity;
            
            if ($newQuantity > $variant->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng vượt quá tồn kho. Còn lại: ' . $variant->stock . ' sản phẩm.'
                ], 400);
            }
            
            $existingItem->update(['quantity' => $newQuantity]);
        } else {
            // Add new item
            $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => $request->quantity,
                'price' => $variant->product->sale_price ?? $variant->product->base_price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng!',
            'cart_count' => $cart->fresh()->item_count,
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        \Log::info('Cart update request', [
            'cart_item_id' => $cartItem->id,
            'quantity' => $request->quantity,
            'user_id' => auth()->id(),
            'session_id' => session()->getId()
        ]);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        // Check if user owns this cart item
        $cart = $this->getOrCreateCart();
        \Log::info('Cart ownership check', [
            'cart_item_cart_id' => $cartItem->cart_id,
            'current_cart_id' => $cart->id,
            'match' => $cartItem->cart_id === $cart->id
        ]);
        
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['success' => false, 'message' => 'Không có quyền truy cập.'], 403);
        }

        // Check stock
        if ($cartItem->variant->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Không đủ hàng trong kho. Còn lại: ' . $cartItem->variant->stock . ' sản phẩm.'
            ], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng!',
            'cart_count' => $cartItem->cart->fresh()->item_count,
            'subtotal' => $cartItem->fresh()->subtotal,
            'total' => $cartItem->cart->fresh()->total,
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove(CartItem $cartItem): JsonResponse
    {
        // Check if user owns this cart item
        $cart = $this->getOrCreateCart();
        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['success' => false, 'message' => 'Không có quyền truy cập.'], 403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi giỏ hàng!',
            'cart_count' => $cartItem->cart->fresh()->item_count,
            'total' => $cartItem->cart->fresh()->total,
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear(): JsonResponse
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa tất cả sản phẩm khỏi giỏ hàng!',
            'cart_count' => 0,
            'total' => 0,
        ]);
    }

    /**
     * Get cart count for header
     */
    public function count(): JsonResponse
    {
        $cart = $this->getOrCreateCart();
        
        return response()->json([
            'count' => $cart->item_count,
        ]);
    }

    /**
     * Get or create cart for current user/session
     */
    private function getOrCreateCart(): Cart
    {
        if (auth()->check()) {
            // User is logged in
            $cart = Cart::where('user_id', auth()->id())->first();
            
            if (!$cart) {
                $cart = Cart::create(['user_id' => auth()->id()]);
            }
            
            // Merge session cart if exists
            $sessionCart = Cart::where('session_id', session()->getId())
                ->whereNull('user_id')
                ->first();
                
            if ($sessionCart && $sessionCart->id !== $cart->id) {
                $this->mergeCarts($cart, $sessionCart);
            }
        } else {
            // Guest user
            $cart = Cart::where('session_id', session()->getId())->first();
            
            if (!$cart) {
                $cart = Cart::create(['session_id' => session()->getId()]);
            }
        }
        
        return $cart;
    }

    /**
     * Merge session cart into user cart
     */
    private function mergeCarts(Cart $userCart, Cart $sessionCart): void
    {
        foreach ($sessionCart->items as $sessionItem) {
            $existingItem = $userCart->items()
                ->where('product_variant_id', $sessionItem->product_variant_id)
                ->first();
                
            if ($existingItem) {
                // Update quantity
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $sessionItem->quantity
                ]);
            } else {
                // Move item to user cart
                $sessionItem->update(['cart_id' => $userCart->id]);
            }
        }
        
        // Delete session cart
        $sessionCart->delete();
    }
}