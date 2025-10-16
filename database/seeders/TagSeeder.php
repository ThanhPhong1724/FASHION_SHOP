<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Mới',
            'Hot',
            'Sale',
            'Premium',
            'Basic',
            'Trendy',
            'Classic',
            'Casual',
            'Formal',
            'Sport',
            'Vintage',
            'Modern',
            'Elegant',
            'Comfortable',
            'Fashionable',
            'Luxury',
            'Affordable',
            'Limited Edition',
            'Best Seller',
            'Recommended',
        ];

        foreach ($tags as $tagName) {
            Tag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName),
            ]);
        }
    }
}