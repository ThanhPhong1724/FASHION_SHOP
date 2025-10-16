<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Root categories
            ['name' => 'Thời trang nam', 'children' => [
                ['name' => 'Áo thun'],
                ['name' => 'Áo sơ mi'],
                ['name' => 'Quần jean'],
                ['name' => 'Quần short'],
                ['name' => 'Áo khoác'],
            ]],
            ['name' => 'Thời trang nữ', 'children' => [
                ['name' => 'Váy'],
                ['name' => 'Áo thun nữ'],
                ['name' => 'Quần jean nữ'],
                ['name' => 'Áo sơ mi nữ'],
                ['name' => 'Áo khoác nữ'],
            ]],
            ['name' => 'Giày dép', 'children' => [
                ['name' => 'Giày thể thao'],
                ['name' => 'Giày tây'],
                ['name' => 'Sandal'],
                ['name' => 'Boots'],
            ]],
            ['name' => 'Phụ kiện', 'children' => [
                ['name' => 'Túi xách'],
                ['name' => 'Ví'],
                ['name' => 'Đồng hồ'],
                ['name' => 'Kính mắt'],
            ]],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => 'Mô tả cho ' . $categoryData['name'],
                'is_active' => true,
            ]);

            if (isset($categoryData['children'])) {
                foreach ($categoryData['children'] as $childData) {
                    Category::create([
                        'parent_id' => $category->id,
                        'name' => $childData['name'],
                        'slug' => Str::slug($childData['name']),
                        'description' => 'Mô tả cho ' . $childData['name'],
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}