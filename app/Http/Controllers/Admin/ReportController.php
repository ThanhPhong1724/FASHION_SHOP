<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display sales report
     */
    public function sales(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $period = $request->get('period', 'daily'); // daily, weekly, monthly

        // Sales summary
        $salesSummary = $this->getSalesSummary($dateFrom, $dateTo);
        
        // Sales chart data
        $chartData = $this->getSalesChartData($dateFrom, $dateTo, $period);
        
        // Top products
        $topProducts = $this->getTopProducts($dateFrom, $dateTo);
        
        // Payment methods
        $paymentMethods = $this->getPaymentMethodsData($dateFrom, $dateTo);

        return view('admin.reports.sales', compact(
            'salesSummary', 
            'chartData', 
            'topProducts', 
            'paymentMethods',
            'dateFrom',
            'dateTo',
            'period'
        ));
    }

    /**
     * Display products report
     */
    public function products(Request $request): View
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // Best selling products
        $bestSelling = $this->getBestSellingProducts($dateFrom, $dateTo);
        
        // Low stock products
        $lowStock = $this->getLowStockProducts();
        
        // Product performance
        $productPerformance = $this->getProductPerformance($dateFrom, $dateTo);

        return view('admin.reports.products', compact(
            'bestSelling',
            'lowStock', 
            'productPerformance',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Export sales report to CSV
     */
    public function exportSales(Request $request): Response
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $orders = Order::whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['user', 'items.variant.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'sales_report_' . $dateFrom . '_to_' . $dateTo . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Mã đơn hàng',
                'Ngày đặt',
                'Khách hàng',
                'Email',
                'Sản phẩm',
                'Số lượng',
                'Đơn giá',
                'Thành tiền',
                'Tổng đơn hàng',
                'Trạng thái',
                'Phương thức thanh toán'
            ]);

            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    fputcsv($file, [
                        $order->order_number,
                        $order->created_at->format('d/m/Y H:i'),
                        $order->user ? $order->user->name : $order->guest_email,
                        $order->user ? $order->user->email : $order->guest_email,
                        $item->product_name,
                        $item->quantity,
                        number_format($item->unit_price, 0, ',', '.'),
                        number_format($item->subtotal, 0, ',', '.'),
                        number_format($order->total, 0, ',', '.'),
                        $order->status_label,
                        $order->payment_method_label
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export products report to CSV
     */
    public function exportProducts(Request $request): Response
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $products = Product::with(['category', 'brand'])
            ->withCount(['orderItems as total_sold' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereHas('order', function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('created_at', [$dateFrom, $dateTo]);
                });
            }])
            ->withSum(['orderItems as total_revenue' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereHas('order', function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('created_at', [$dateFrom, $dateTo]);
                });
            }], 'subtotal')
            ->orderBy('total_sold', 'desc')
            ->get();

        $filename = 'products_report_' . $dateFrom . '_to_' . $dateTo . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Tên sản phẩm',
                'SKU',
                'Danh mục',
                'Thương hiệu',
                'Giá bán',
                'Số lượng đã bán',
                'Doanh thu',
                'Lượt xem',
                'Trạng thái'
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->sku,
                    $product->category->name,
                    $product->brand->name,
                    number_format($product->base_price, 0, ',', '.'),
                    $product->total_sold,
                    number_format($product->total_revenue ?? 0, 0, ',', '.'),
                    $product->views_count,
                    $product->is_active ? 'Hoạt động' : 'Không hoạt động'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get sales summary data
     */
    private function getSalesSummary(string $dateFrom, string $dateTo): array
    {
        $orders = Order::whereBetween('created_at', [$dateFrom, $dateTo]);

        return [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total'),
            'average_order_value' => $orders->avg('total') ?? 0,
            'completed_orders' => $orders->where('status', 'completed')->count(),
            'pending_orders' => $orders->whereIn('status', ['pending', 'confirmed', 'processing', 'shipping'])->count(),
            'cancelled_orders' => $orders->where('status', 'cancelled')->count(),
        ];
    }

    /**
     * Get sales chart data
     */
    private function getSalesChartData(string $dateFrom, string $dateTo, string $period): array
    {
        $query = Order::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed');

        switch ($period) {
            case 'daily':
                $data = $query->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                break;
            case 'weekly':
                $data = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%u") as week, COUNT(*) as orders, SUM(total) as revenue')
                    ->groupBy('week')
                    ->orderBy('week')
                    ->get();
                break;
            case 'monthly':
                $data = $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as orders, SUM(total) as revenue')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                break;
            default:
                $data = collect();
        }

        return [
            'labels' => $data->pluck($period === 'daily' ? 'date' : ($period === 'weekly' ? 'week' : 'month')),
            'orders' => $data->pluck('orders'),
            'revenue' => $data->pluck('revenue')
        ];
    }

    /**
     * Get top products data
     */
    private function getTopProducts(string $dateFrom, string $dateTo): array
    {
        return OrderItem::whereHas('order', function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo])
                      ->where('status', 'completed');
            })
            ->selectRaw('product_name, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->groupBy('product_name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get payment methods data
     */
    private function getPaymentMethodsData(string $dateFrom, string $dateTo): array
    {
        return Order::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as revenue')
            ->groupBy('payment_method')
            ->get()
            ->toArray();
    }

    /**
     * Get best selling products
     */
    private function getBestSellingProducts(string $dateFrom, string $dateTo)
    {
        return Product::with(['category', 'brand'])
            ->withCount(['orderItems as total_sold' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereHas('order', function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('created_at', [$dateFrom, $dateTo])
                      ->where('status', 'completed');
                });
            }])
            ->withSum(['orderItems as total_revenue' => function ($query) use ($dateFrom, $dateTo) {
                $query->whereHas('order', function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('created_at', [$dateFrom, $dateTo])
                      ->where('status', 'completed');
                });
            }], 'subtotal')
            ->orderBy('total_sold', 'desc')
            ->limit(20)
            ->get();
    }

    /**
     * Get low stock products
     */
    private function getLowStockProducts()
    {
        return Product::with(['variants'])
            ->whereHas('variants', function ($query) {
                $query->where('stock', '<=', 10);
            })
            ->with(['variants' => function ($query) {
                $query->where('stock', '<=', 10);
            }])
            ->get();
    }

    /**
     * Get product performance data
     */
    private function getProductPerformance(string $dateFrom, string $dateTo): array
    {
        return [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'products_with_sales' => Product::whereHas('orderItems.order', function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            })->count(),
            'average_views' => Product::avg('views_count') ?? 0,
        ];
    }
}