<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $orders = Auth::user()->orders()
            ->with(['items.variant.product', 'shippingAddress', 'billingAddress'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): View
    {
        // Kiểm tra user có quyền xem order này không
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        $order->load([
            'items.variant.product',
            'shippingAddress',
            'billingAddress',
            'coupons'
        ]);

        return view('orders.show', compact('order'));
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(Order $order): RedirectResponse
    {
        // Kiểm tra user có quyền hủy order này không
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền hủy đơn hàng này.');
        }

        // Chỉ cho phép hủy đơn hàng ở trạng thái pending
        if ($order->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng đang chờ xử lý.');
        }

        DB::transaction(function () use ($order) {
            // Hoàn trả tồn kho
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->increment('stock', $item->quantity);
                }
            }

            // Cập nhật trạng thái đơn hàng
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);
        });

        return back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }

    /**
     * Reorder the specified order.
     */
    public function reorder(Order $order): RedirectResponse
    {
        // Kiểm tra user có quyền reorder không
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền đặt lại đơn hàng này.');
        }

        // Chỉ cho phép reorder đơn hàng đã hoàn thành
        if ($order->status !== 'completed') {
            return back()->with('error', 'Chỉ có thể đặt lại đơn hàng đã hoàn thành.');
        }

        // Thêm các sản phẩm từ đơn hàng cũ vào giỏ hàng
        foreach ($order->items as $item) {
            if ($item->variant && $item->variant->is_active && $item->variant->stock > 0) {
                // Sử dụng Cart facade hoặc service
                \Cart::add([
                    'id' => $item->variant->id,
                    'name' => $item->product_name,
                    'qty' => $item->quantity,
                    'price' => $item->variant->final_price,
                    'options' => [
                        'size' => $item->variant->size,
                        'color' => $item->variant->color,
                        'image' => $item->variant->product->getFirstMediaUrl('images', 'preview')
                    ]
                ]);
            }
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }
}
