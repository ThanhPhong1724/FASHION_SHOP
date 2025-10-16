<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Display search results
     */
    public function index(Request $request): View
    {
        $query = $request->get('q', '');
        $category = $request->get('category');
        $brand = $request->get('brand');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $sort = $request->get('sort', 'relevance');

        $products = collect();

        if (!empty($query)) {
            $products = Product::with(['category', 'brand', 'tags'])
                ->active()
                ->withCount(['variants', 'approvedReviews'])
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('short_description', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('sku', 'like', "%{$query}%")
                      ->orWhereHas('category', function ($subQ) use ($query) {
                          $subQ->where('name', 'like', "%{$query}%");
                      })
                      ->orWhereHas('brand', function ($subQ) use ($query) {
                          $subQ->where('name', 'like', "%{$query}%");
                      })
                      ->orWhereHas('tags', function ($subQ) use ($query) {
                          $subQ->where('name', 'like', "%{$query}%");
                      });
                });

            // Apply filters
            if ($category) {
                $products->where('category_id', $category);
            }

            if ($brand) {
                $products->where('brand_id', $brand);
            }

            if ($minPrice || $maxPrice) {
                $products->where(function ($q) use ($minPrice, $maxPrice) {
                    if ($minPrice) {
                        $q->where('base_price', '>=', $minPrice);
                    }
                    if ($maxPrice) {
                        $q->where('base_price', '<=', $maxPrice);
                    }
                });
            }

            // Sort
            switch ($sort) {
                case 'price_asc':
                    $products->orderByRaw('COALESCE(sale_price, base_price) ASC');
                    break;
                case 'price_desc':
                    $products->orderByRaw('COALESCE(sale_price, base_price) DESC');
                    break;
                case 'name_asc':
                    $products->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $products->orderBy('name', 'desc');
                    break;
                case 'newest':
                    $products->orderBy('created_at', 'desc');
                    break;
                default: // relevance
                    $products->orderByRaw("
                        CASE 
                            WHEN name LIKE ? THEN 1
                            WHEN short_description LIKE ? THEN 2
                            WHEN description LIKE ? THEN 3
                            ELSE 4
                        END
                    ", ["%{$query}%", "%{$query}%", "%{$query}%"]);
                    break;
            }

            $products = $products->paginate(12)->withQueryString();
        }

        // Get filter options
        $categories = Category::active()->get();
        $brands = Brand::active()->get();

        // Price range
        $priceRange = [
            'min' => Product::active()->min('base_price') ?? 0,
            'max' => Product::active()->max('base_price') ?? 1000000,
        ];

        return view('search.index', compact(
            'products',
            'categories',
            'brands',
            'priceRange',
            'query'
        ));
    }

    /**
     * AJAX search suggestions
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = collect();

        // Product suggestions
        $products = Product::active()
            ->where('name', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'type' => 'product',
                    'id' => $product->id,
                    'name' => $product->name,
                    'url' => route('products.show', $product),
                    'image' => $product->getFirstMediaUrl('images', 'preview'),
                    'price' => $product->final_price,
                ];
            });

        $suggestions = $suggestions->merge($products);

        // Category suggestions
        $categories = Category::active()
            ->where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get()
            ->map(function ($category) {
                return [
                    'type' => 'category',
                    'id' => $category->id,
                    'name' => $category->name,
                    'url' => route('categories.show', $category),
                ];
            });

        $suggestions = $suggestions->merge($categories);

        // Brand suggestions
        $brands = Brand::active()
            ->where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get()
            ->map(function ($brand) {
                return [
                    'type' => 'brand',
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'url' => route('brands.show', $brand),
                ];
            });

        $suggestions = $suggestions->merge($brands);

        return response()->json($suggestions->take(10)->values());
    }

    /**
     * Popular searches
     */
    public function popular(): JsonResponse
    {
        $popularSearches = [
            'áo thun nam',
            'quần jean nữ',
            'giày sneaker',
            'túi xách',
            'đồng hồ',
            'kính mát',
            'áo khoác',
            'váy đầm',
        ];

        return response()->json($popularSearches);
    }
}
