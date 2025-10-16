<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some products and users
        $products = Product::take(5)->get();
        $users = User::where('email', '!=', 'admin@fashion.test')->take(3)->get();

        if ($products->isEmpty() || $users->isEmpty()) {
            $this->command->info('Không có sản phẩm hoặc user để tạo reviews. Vui lòng chạy ProductSeeder và UserSeeder trước.');
            return;
        }

        $reviews = [
            [
                'rating' => 5,
                'title' => 'Sản phẩm tuyệt vời!',
                'content' => 'Chất lượng rất tốt, đúng như mô tả. Giao hàng nhanh, đóng gói cẩn thận. Sẽ mua lại lần sau.',
                'status' => 'approved',
                'is_verified_purchase' => true
            ],
            [
                'rating' => 4,
                'title' => 'Tốt, nhưng có thể cải thiện',
                'content' => 'Sản phẩm đẹp, chất lượng ổn. Tuy nhiên size hơi nhỏ so với mong đợi. Nên chọn size lớn hơn.',
                'status' => 'approved',
                'is_verified_purchase' => true
            ],
            [
                'rating' => 5,
                'title' => 'Hoàn hảo!',
                'content' => 'Rất hài lòng với sản phẩm này. Thiết kế đẹp, chất liệu tốt, giá cả hợp lý. Khuyến nghị mọi người nên mua.',
                'status' => 'approved',
                'is_verified_purchase' => true
            ],
            [
                'rating' => 3,
                'title' => 'Bình thường',
                'content' => 'Sản phẩm ổn, không có gì đặc biệt. Giá cả phù hợp với chất lượng.',
                'status' => 'approved',
                'is_verified_purchase' => true
            ],
            [
                'rating' => 2,
                'title' => 'Không như mong đợi',
                'content' => 'Chất lượng không tốt như quảng cáo. Màu sắc khác với hình ảnh. Không hài lòng lắm.',
                'status' => 'pending',
                'is_verified_purchase' => true
            ],
            [
                'rating' => 4,
                'title' => 'Đáng mua',
                'content' => 'Sản phẩm tốt, thiết kế đẹp. Chỉ có điều giao hàng hơi chậm một chút.',
                'status' => 'approved',
                'is_verified_purchase' => true
            ],
            [
                'rating' => 5,
                'title' => 'Xuất sắc!',
                'content' => 'Chất lượng vượt mong đợi. Rất đẹp và bền. Đóng gói cẩn thận, giao hàng nhanh.',
                'status' => 'approved',
                'is_verified_purchase' => true
            ],
            [
                'rating' => 1,
                'title' => 'Rất tệ',
                'content' => 'Sản phẩm kém chất lượng, không đúng mô tả. Rất thất vọng.',
                'status' => 'rejected',
                'is_verified_purchase' => true
            ]
        ];

        foreach ($products as $index => $product) {
            // Tạo 2-3 reviews cho mỗi sản phẩm
            $reviewCount = rand(2, 3);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                $user = $users->random();
                $reviewData = $reviews[array_rand($reviews)];
                
                // Tạo address giả
                $address = Address::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'phone' => '0123456789',
                    'address_line1' => '123 Đường ABC',
                    'address_line2' => 'Phường XYZ',
                    'city' => 'Hồ Chí Minh',
                    'district' => 'Quận 1',
                    'ward' => 'Phường Bến Nghé',
                    'postal_code' => '700000',
                    'is_default' => true,
                    'type' => 'shipping'
                ]);

                // Tạo order giả để verify purchase
                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => 'ORD-' . time() . '-' . rand(1000, 9999),
                    'status' => 'completed',
                    'subtotal' => $product->base_price,
                    'discount' => 0,
                    'shipping_fee' => 30000,
                    'tax' => 0,
                    'total' => $product->base_price + 30000,
                    'payment_method' => 'cod',
                    'payment_status' => 'paid',
                    'shipping_address_id' => $address->id,
                    'billing_address_id' => $address->id,
                    'notes' => 'Order for review seeder'
                ]);

                // Tạo order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $product->variants->first()?->id,
                    'product_name' => $product->name,
                    'variant_details' => json_encode(['size' => 'M', 'color' => 'Đen']),
                    'quantity' => 1,
                    'unit_price' => $product->base_price,
                    'subtotal' => $product->base_price
                ]);

                // Tạo review
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'rating' => $reviewData['rating'],
                    'title' => $reviewData['title'],
                    'content' => $reviewData['content'],
                    'status' => $reviewData['status'],
                    'is_verified_purchase' => $reviewData['is_verified_purchase']
                ]);
            }
        }

        $this->command->info('Đã tạo ' . ($products->count() * 2) . ' reviews mẫu.');
    }
}