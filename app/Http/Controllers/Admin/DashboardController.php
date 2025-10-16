<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Today's stats
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total');
        
        // Total stats
        $totalProducts = Product::count();
        $totalUsers = User::count();
        $totalCategories = Category::count();
        $totalBrands = Brand::count();
        
        // Recent orders
        $recentOrders = Order::with(['user', 'items.variant.product'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Top selling products
        $topProducts = Product::with('category', 'brand')
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();
        
        // Orders by status
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        // Revenue by month (last 6 months) - MySQL compatible
        $monthlyRevenue = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as revenue')
            )
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'todayOrders',
            'todayRevenue',
            'totalProducts',
            'totalUsers',
            'totalCategories',
            'totalBrands',
            'recentOrders',
            'topProducts',
            'ordersByStatus',
            'monthlyRevenue'
        ));
    }
}