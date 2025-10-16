<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'brand', 'tags'])
            ->active()
            ->withCount(['variants', 'approvedReviews']);

        // Advanced Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('brand', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('tags', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Category filter (including subcategories)
        if ($request->filled('category')) {
            $categoryId = $request->category;
            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                  ->orWhereHas('category', function ($subQ) use ($categoryId) {
                      $subQ->where('parent_id', $categoryId);
                  });
            });
        }

        // Brand filter
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Price range filter (considering sale price)
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->where(function ($q) use ($request) {
                $minPrice = $request->min_price;
                $maxPrice = $request->max_price;
                
                if ($minPrice) {
                    $q->where(function ($subQ) use ($minPrice) {
                        $subQ->where('base_price', '>=', $minPrice)
                             ->orWhere(function ($saleQ) use ($minPrice) {
                                 $saleQ->whereNotNull('sale_price')
                                       ->where('sale_price', '>=', $minPrice);
                             });
                    });
                }
                
                if ($maxPrice) {
                    $q->where(function ($subQ) use ($maxPrice) {
                        $subQ->where('base_price', '<=', $maxPrice)
                             ->orWhere(function ($saleQ) use ($maxPrice) {
                                 $saleQ->whereNotNull('sale_price')
                                       ->where('sale_price', '<=', $maxPrice);
                             });
                    });
                }
            });
        }

        // Size filter
        if ($request->filled('size')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('size', $request->size)->where('stock', '>', 0);
            });
        }

        // Color filter
        if ($request->filled('color')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('color', 'like', "%{$request->color}%")->where('stock', '>', 0);
            });
        }

        // Tags filter
        if ($request->filled('tags')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->whereIn('tags.id', $request->tags);
            });
        }

        // Featured filter
        if ($request->filled('featured')) {
            $query->featured();
        }

        // In stock filter
        if ($request->filled('in_stock')) {
            $query->whereHas('variants', function ($q) {
                $q->where('stock', '>', 0);
            });
        }

        // On sale filter
        if ($request->filled('on_sale')) {
            $query->whereNotNull('sale_price')
                  ->where('sale_price', '<', \DB::raw('base_price'));
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(sale_price, base_price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(sale_price, base_price) DESC');
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
            case 'popular':
                $query->orderBy('sales_count', 'desc')->orderBy('views_count', 'desc');
                break;
            case 'rating':
                $query->leftJoin('reviews', 'products.id', '=', 'reviews.product_id')
                      ->where('reviews.status', 'approved')
                      ->selectRaw('products.*, AVG(reviews.rating) as avg_rating')
                      ->groupBy('products.id')
                      ->orderBy('avg_rating', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        // Get filter options
        $categories = Category::active()->with('children')->whereNull('parent_id')->get();
        $brands = Brand::active()->get();
        $tags = Tag::all();
        
        // Get available sizes and colors
        $sizes = Product::active()
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->where('product_variants.stock', '>', 0)
            ->whereNotNull('product_variants.size')
            ->distinct()
            ->pluck('product_variants.size')
            ->filter()
            ->sort()
            ->values();

        $colors = Product::active()
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->where('product_variants.stock', '>', 0)
            ->whereNotNull('product_variants.color')
            ->distinct()
            ->pluck('product_variants.color')
            ->filter()
            ->sort()
            ->values();

        // Price range
        $priceRange = [
            'min' => Product::active()->min('base_price') ?? 0,
            'max' => Product::active()->max('base_price') ?? 1000000,
        ];

        return view('products.index', compact(
            'products',
            'categories',
            'brands',
            'tags',
            'sizes',
            'colors',
            'priceRange'
        ));
    }

    /**
     * Display the specified product
     */
    public function show(Product $product): View
    {
        // Only show active products
        if (!$product->is_active) {
            abort(404);
        }

        $product->load(['category', 'brand', 'tags', 'variants', 'approvedReviews.user', 'approvedReviews.images']);
        
        // Increment view count
        $product->increment('views_count');

        // Get related products
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'brand'])
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
