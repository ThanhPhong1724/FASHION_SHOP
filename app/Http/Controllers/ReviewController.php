<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Store a newly created review
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:2000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Check if user has purchased this product
        $hasPurchased = OrderItem::whereHas('order', function($query) {
            $query->where('user_id', Auth::id())
                  ->where('status', 'completed');
        })->where('product_name', $product->name)->exists();

        if (!$hasPurchased) {
            return back()->withErrors(['review' => 'Bạn phải mua sản phẩm này mới có thể đánh giá!']);
        }

        // Check if user already reviewed this product
        $existingReview = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return back()->withErrors(['review' => 'Bạn đã đánh giá sản phẩm này rồi!']);
        }

        // Create review
        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'title' => $request->title,
            'content' => $request->content,
            'status' => 'pending',
            'is_verified_purchase' => true
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                
                // Create review image record
                $review->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        return back()->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt!');
    }

    /**
     * Show the form for editing a review
     */
    public function edit(Review $review)
    {
        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, Review $review): RedirectResponse
    {
        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:2000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $review->update([
            'rating' => $request->rating,
            'title' => $request->title,
            'content' => $request->content,
            'status' => 'pending' // Reset to pending after edit
        ]);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                
                $review->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->route('products.show', $review->product)
            ->with('success', 'Đánh giá đã được cập nhật!');
    }

    /**
     * Remove the specified review
     */
    public function destroy(Review $review): RedirectResponse
    {
        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete associated images
        foreach ($review->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $review->delete();

        return back()->with('success', 'Đánh giá đã được xóa!');
    }

    /**
     * Delete a review image
     */
    public function deleteImage(Request $request, Review $review): RedirectResponse
    {
        $request->validate([
            'image_id' => 'required|exists:review_images,id'
        ]);

        // Check if user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $image = $review->images()->findOrFail($request->image_id);
        
        // Delete file from storage
        Storage::disk('public')->delete($image->image_path);
        
        // Delete record
        $image->delete();

        return back()->with('success', 'Ảnh đã được xóa!');
    }
}