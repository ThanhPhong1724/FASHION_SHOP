<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display products in a category
     */
    public function show(Category $category, Request $request): View
    {
        // Only show active categories
        if (!$category->is_active) {
            abort(404);
        }

        $query = Product::with(['category', 'brand', 'tags'])
            ->active()
            ->where('category_id', $category->id)
            ->withCount('variants');

        // Search within category
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Brand filter
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
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

        // Get brands available in this category
        $brands = Brand::active()
            ->whereHas('products', function ($q) use ($category) {
                $q->where('category_id', $category->id)->active();
            })
            ->withCount(['products' => function ($q) use ($category) {
                $q->where('category_id', $category->id)->active();
            }])
            ->get();

        // Price range for this category
        $priceRange = [
            'min' => Product::active()->where('category_id', $category->id)->min('base_price') ?? 0,
            'max' => Product::active()->where('category_id', $category->id)->max('base_price') ?? 1000000,
        ];

        return view('categories.show', compact('category', 'products', 'brands', 'priceRange'));
    }
}
