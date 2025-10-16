<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $products = Product::all();
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'user');
        })->get();

        if ($users->count() === 0) {
            // Create some test users if none exist
            for ($i = 0; $i < 10; $i++) {
                $user = User::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('user');
                $users->push($user);
            }
        }

        $reviews = [
            'Chất lượng tốt, giao hàng nhanh!',
            'Sản phẩm đẹp, đúng như mô tả.',
            'Rất hài lòng với sản phẩm này.',
            'Chất liệu tốt, giá cả hợp lý.',
            'Sẽ mua lại lần sau.',
            'Sản phẩm chất lượng cao.',
            'Đóng gói cẩn thận, giao hàng đúng hẹn.',
            'Màu sắc đẹp, form dáng chuẩn.',
            'Giá tốt, chất lượng ổn.',
            'Sản phẩm đúng như hình ảnh.',
        ];

        foreach ($products as $product) {
            // Create 2-5 reviews per product
            $reviewCount = rand(2, 5);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $users->random()->id,
                    'order_id' => null, // No orders table yet
                    'rating' => rand(3, 5), // Mostly positive reviews
                    'title' => $faker->optional(0.7)->sentence(),
                    'content' => $faker->randomElement($reviews),
                    'status' => 'approved',
                    'is_verified_purchase' => $faker->boolean(80), // 80% verified purchase
                ]);
            }
        }
    }
}