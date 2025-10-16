<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist
     */
    public function index(): View
    {
        $wishlistItems = Auth::user()->wishlistProducts()
            ->whereNotNull('products.id') // Chỉ lấy products còn tồn tại
            ->with(['category', 'brand', 'media'])
            ->paginate(12);

        return view('wishlist.index', compact('wishlistItems'));
    }

    /**
     * Add product to wishlist
     */
    public function store(Request $request, Product $product): JsonResponse
    {
        $user = Auth::user();
        
        // Check if product is active
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm này hiện không khả dụng'
            ]);
        }
        
        // Check if already in wishlist
        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm đã có trong danh sách yêu thích'
            ]);
        }

        // Add to wishlist
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào danh sách yêu thích',
            'wishlist_count' => $user->wishlists()->count()
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        $user = Auth::user();
        
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if (!$wishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không có trong danh sách yêu thích'
            ]);
        }

        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa khỏi danh sách yêu thích',
            'wishlist_count' => $user->wishlists()->count()
        ]);
    }

    /**
     * Toggle product in wishlist (add if not exists, remove if exists)
     */
    public function toggle(Request $request, Product $product): JsonResponse
    {
        $user = Auth::user();
        
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlistItem) {
            // Remove from wishlist
            $wishlistItem->delete();
            $action = 'removed';
            $message = 'Đã xóa khỏi danh sách yêu thích';
        } else {
            // Add to wishlist
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id
            ]);
            $action = 'added';
            $message = 'Đã thêm vào danh sách yêu thích';
        }

        return response()->json([
            'success' => true,
            'action' => $action,
            'message' => $message,
            'wishlist_count' => $user->wishlists()->count()
        ]);
    }

    /**
     * Check if product is in user's wishlist
     */
    public function check(Request $request, Product $product): JsonResponse
    {
        $user = Auth::user();
        
        $inWishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        return response()->json([
            'in_wishlist' => $inWishlist
        ]);
    }
}
