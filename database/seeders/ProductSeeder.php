<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Tag;
use App\Models\ProductVariant;
use App\Services\SkuGeneratorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        
        $categories = Category::all();
        $brands = Brand::all();
        $tags = Tag::all();
        
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $colors = ['Đen', 'Trắng', 'Xanh', 'Đỏ', 'Vàng', 'Hồng', 'Xám', 'Nâu'];
        
        for ($i = 0; $i < 50; $i++) {
            $name = $faker->words(3, true);
            $category = $categories->random();
            $brand = $brands->random();
            
            $product = Product::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'sku' => SkuGeneratorService::generate(),
                'short_description' => $faker->sentence(),
                'description' => $faker->paragraphs(3, true),
                'base_price' => $faker->numberBetween(100000, 2000000),
                'sale_price' => $faker->optional(0.3)->numberBetween(50000, 1500000),
                'is_active' => $faker->boolean(90),
                'is_featured' => $faker->boolean(20),
                'meta_title' => $faker->sentence(),
                'meta_description' => $faker->sentence(),
            ]);
            
            // Attach random tags
            $product->tags()->attach($tags->random(rand(1, 3))->pluck('id'));
            
            // Create variants
            $variantCount = rand(2, 6);
            for ($j = 0; $j < $variantCount; $j++) {
                $size = $faker->randomElement($sizes);
                $color = $faker->randomElement($colors);
                
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $size,
                    'color' => $color,
                    'sku' => SkuGeneratorService::generateVariant($product->sku, $size, $color),
                    'stock' => $faker->numberBetween(0, 100),
                    'price_delta' => $faker->optional(0.3)->numberBetween(-50000, 50000) ?? 0,
                ]);
            }
        }
    }
}
