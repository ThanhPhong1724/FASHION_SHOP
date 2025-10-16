<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Tag;
use App\Models\ProductVariant;
use App\Services\SkuGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'tags', 'variants'])
            ->withCount('variants')
            ->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        $tags = Tag::all();
        return view('admin.products.create', compact('categories', 'brands', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'variants' => 'nullable|array',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.sku' => 'nullable|string|max:255',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.price_delta' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request) {
            // Create product
            $product = Product::create([
                'name' => $request->name,
                'slug' => $request->slug ?? Str::slug($request->name),
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'sku' => $request->sku ?? SkuGeneratorService::generate(),
                'short_description' => $request->short_description,
                'description' => $request->description,
                'base_price' => $request->base_price,
                'sale_price' => $request->sale_price,
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
            ]);

            // Attach tags
            if ($request->tags) {
                $product->tags()->attach($request->tags);
            }

            // Upload images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image)->toMediaCollection('images');
                }
            }

            // Create variants
            if ($request->variants) {
                foreach ($request->variants as $variantData) {
                    if (!empty($variantData['size']) || !empty($variantData['color'])) {
                        $variantSku = $variantData['sku'] ?? SkuGeneratorService::generateVariant(
                            $product->sku, 
                            $variantData['size'] ?? null, 
                            $variantData['color'] ?? null
                        );
                        
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size' => $variantData['size'] ?? null,
                            'color' => $variantData['color'] ?? null,
                            'sku' => $variantSku,
                            'stock' => $variantData['stock'] ?? 0,
                            'price_delta' => $variantData['price_delta'] ?? 0,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được tạo thành công.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'tags', 'variants', 'images']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['tags', 'variants']);
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        $tags = Tag::all();
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'tags'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.sku' => 'nullable|string|max:255',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.price_delta' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request, $product) {
            // Update product
            $product->update([
                'name' => $request->name,
                'slug' => $request->slug ?? Str::slug($request->name),
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'sku' => $request->sku,
                'short_description' => $request->short_description,
                'description' => $request->description,
                'base_price' => $request->base_price,
                'sale_price' => $request->sale_price,
                'is_active' => $request->has('is_active'),
                'is_featured' => $request->has('is_featured'),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
            ]);

            // Sync tags
            if ($request->tags) {
                $product->tags()->sync($request->tags);
            } else {
                $product->tags()->detach();
            }

            // Upload new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image)->toMediaCollection('images');
                }
            }

            // Update variants
            if ($request->variants) {
                $existingVariantIds = [];
                
                foreach ($request->variants as $variantData) {
                    if (!empty($variantData['size']) || !empty($variantData['color'])) {
                        if (isset($variantData['id']) && $variantData['id']) {
                            // Update existing variant
                            $variant = ProductVariant::find($variantData['id']);
                            if ($variant) {
                                $variant->update([
                                    'size' => $variantData['size'] ?? null,
                                    'color' => $variantData['color'] ?? null,
                                    'sku' => $variantData['sku'] ?? null,
                                    'stock' => $variantData['stock'] ?? 0,
                                    'price_delta' => $variantData['price_delta'] ?? 0,
                                ]);
                                $existingVariantIds[] = $variant->id;
                            }
                        } else {
                            // Create new variant
                            $variantSku = $variantData['sku'] ?? SkuGeneratorService::generateVariant(
                                $product->sku, 
                                $variantData['size'] ?? null, 
                                $variantData['color'] ?? null
                            );
                            
                            $variant = ProductVariant::create([
                                'product_id' => $product->id,
                                'size' => $variantData['size'] ?? null,
                                'color' => $variantData['color'] ?? null,
                                'sku' => $variantSku,
                                'stock' => $variantData['stock'] ?? 0,
                                'price_delta' => $variantData['price_delta'] ?? 0,
                            ]);
                            $existingVariantIds[] = $variant->id;
                        }
                    }
                }
                
                // Delete variants not in the request
                $product->variants()->whereNotIn('id', $existingVariantIds)->delete();
            } else {
                // Delete all variants if none provided
                $product->variants()->delete();
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được cập nhật thành công.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được xóa thành công.');
    }
}
