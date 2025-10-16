<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Zara',
            'H&M',
            'Uniqlo',
            'Nike',
            'Adidas',
            'Gucci',
            'Louis Vuitton',
            'Chanel',
            'Prada',
            'Versace',
        ];

        foreach ($brands as $brandName) {
            Brand::create([
                'name' => $brandName,
                'slug' => Str::slug($brandName),
                'description' => 'Thương hiệu ' . $brandName . ' nổi tiếng thế giới',
                'is_active' => true,
            ]);
        }
    }
}