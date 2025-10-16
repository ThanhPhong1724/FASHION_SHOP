<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Review::with(['product', 'user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search by product name or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%");
                })->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $reviews = $query->latest()->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review): View
    {
        $review->load(['product', 'user', 'images']);

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve review
     */
    public function approve(Review $review): RedirectResponse
    {
        $review->update(['status' => 'approved']);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đã duyệt review thành công!');
    }

    /**
     * Reject review
     */
    public function reject(Review $review): RedirectResponse
    {
        $review->update(['status' => 'rejected']);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đã từ chối review!');
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApprove(Request $request): RedirectResponse
    {
        $request->validate([
            'review_ids' => 'required|string'
        ]);

        $reviewIds = json_decode($request->review_ids, true);
        
        if (!is_array($reviewIds) || empty($reviewIds)) {
            return redirect()->route('admin.reviews.index')
                ->with('error', 'Không có review nào được chọn!');
        }

        Review::whereIn('id', $reviewIds)
            ->update(['status' => 'approved']);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đã duyệt ' . count($reviewIds) . ' review thành công!');
    }

    /**
     * Bulk reject reviews
     */
    public function bulkReject(Request $request): RedirectResponse
    {
        $request->validate([
            'review_ids' => 'required|string'
        ]);

        $reviewIds = json_decode($request->review_ids, true);
        
        if (!is_array($reviewIds) || empty($reviewIds)) {
            return redirect()->route('admin.reviews.index')
                ->with('error', 'Không có review nào được chọn!');
        }

        Review::whereIn('id', $reviewIds)
            ->update(['status' => 'rejected']);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đã từ chối ' . count($reviewIds) . ' review!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đã xóa review thành công!');
    }
}