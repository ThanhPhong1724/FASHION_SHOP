<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the homepage
     */
    public function index(): View
    {
        // Featured products
        $featuredProducts = Product::with(['category', 'brand'])
            ->active()
            ->featured()
            ->withCount('variants')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Latest products
        $latestProducts = Product::with(['category', 'brand'])
            ->active()
            ->withCount('variants')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Categories with product count
        $categories = Category::active()
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(6)
            ->get();

        // Brands with product count
        $brands = Brand::active()
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(8)
            ->get();

        return view('home', compact(
            'featuredProducts',
            'latestProducts',
            'categories',
            'brands'
        ));
    }
}
