<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items.variant.product']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'items.variant.product', 'shippingAddress']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipping,completed,cancelled,refunded',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
        ]);

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Đã cập nhật trạng thái đơn hàng!');
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $query = Order::with(['user', 'items.variant.product']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->get();

        $filename = 'orders_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Order Number',
                'Customer',
                'Email',
                'Phone',
                'Status',
                'Payment Method',
                'Payment Status',
                'Subtotal',
                'Shipping Fee',
                'Total',
                'Created At',
                'Items'
            ]);

            foreach ($orders as $order) {
                $customerName = $order->user ? $order->user->name : 'Guest';
                $customerEmail = $order->user ? $order->user->email : $order->guest_email;
                $customerPhone = $order->user ? $order->user->phone : $order->guest_phone;
                
                $items = $order->items->map(function ($item) {
                    return $item->product_name . ' (' . $item->quantity . 'x)';
                })->join('; ');

                fputcsv($file, [
                    $order->order_number,
                    $customerName,
                    $customerEmail,
                    $customerPhone,
                    $order->status_label,
                    $order->payment_method_label,
                    $order->payment_status,
                    $order->subtotal,
                    $order->shipping_fee,
                    $order->total,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $items
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
