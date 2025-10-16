<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    /**
     * Display products by brand
     */
    public function show(Brand $brand, Request $request): View
    {
        // Only show active brands
        if (!$brand->is_active) {
            abort(404);
        }

        $query = Product::with(['category', 'brand', 'tags'])
            ->active()
            ->where('brand_id', $brand->id)
            ->withCount('variants');

        // Search within brand
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        // Get categories available for this brand
        $categories = Category::active()
            ->whereHas('products', function ($q) use ($brand) {
                $q->where('brand_id', $brand->id)->active();
            })
            ->withCount(['products' => function ($q) use ($brand) {
                $q->where('brand_id', $brand->id)->active();
            }])
            ->get();

        // Price range for this brand
        $priceRange = [
            'min' => Product::active()->where('brand_id', $brand->id)->min('base_price') ?? 0,
            'max' => Product::active()->where('brand_id', $brand->id)->max('base_price') ?? 1000000,
        ];

        return view('brands.show', compact('brand', 'products', 'categories', 'priceRange'));
    }
}
