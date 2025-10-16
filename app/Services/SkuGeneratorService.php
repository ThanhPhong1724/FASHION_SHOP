<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class SkuGeneratorService
{
    /**
     * Generate a unique SKU for a product
     */
    public static function generate(Product $product = null, string $prefix = null): string
    {
        // If product is provided, use its data to generate meaningful SKU
        if ($product) {
            $categoryCode = $product->category ? strtoupper(substr($product->category->name, 0, 3)) : 'PRD';
            $brandCode = $product->brand ? strtoupper(substr($product->brand->name, 0, 3)) : 'GEN';
            $nameCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $product->name), 0, 3));
            
            $baseSku = $categoryCode . '-' . $brandCode . '-' . $nameCode;
        } else {
            // Default format: PREFIX-YYYYMMDD-XXXX
            $baseSku = $prefix ? strtoupper($prefix) : 'PRD';
            $baseSku .= '-' . date('Ymd');
        }
        
        // Add random suffix to ensure uniqueness
        $counter = 1;
        do {
            $sku = $baseSku . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
            $counter++;
        } while (Product::where('sku', $sku)->exists());
        
        return $sku;
    }
    
    /**
     * Generate SKU for product variant
     */
    public static function generateVariant(string $productSku, string $size = null, string $color = null): string
    {
        $variantSku = $productSku;
        
        if ($size) {
            $variantSku .= '-' . strtoupper($size);
        }
        
        if ($color) {
            $colorCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $color), 0, 3));
            $variantSku .= '-' . $colorCode;
        }
        
        // Ensure uniqueness by adding counter if needed
        $counter = 1;
        $originalSku = $variantSku;
        while (\App\Models\ProductVariant::where('sku', $variantSku)->exists()) {
            $variantSku = $originalSku . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT);
            $counter++;
        }
        
        return $variantSku;
    }
}
