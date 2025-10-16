<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Chào mừng khách hàng mới',
                'description' => 'Giảm 10% cho đơn hàng đầu tiên của khách hàng mới',
                'type' => 'percentage',
                'value' => 10,
                'min_order_amount' => 200000,
                'max_discount' => 50000,
                'usage_limit' => 1000,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'SALE20',
                'name' => 'Giảm giá 20%',
                'description' => 'Giảm 20% cho tất cả sản phẩm',
                'type' => 'percentage',
                'value' => 20,
                'min_order_amount' => 500000,
                'max_discount' => 200000,
                'usage_limit' => 500,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'FREESHIP',
                'name' => 'Miễn phí vận chuyển',
                'description' => 'Miễn phí vận chuyển cho đơn hàng từ 300k',
                'type' => 'fixed',
                'value' => 30000,
                'min_order_amount' => 300000,
                'max_discount' => null,
                'usage_limit' => 2000,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addDays(60),
                'is_active' => true,
            ],
            [
                'code' => 'VIP50',
                'name' => 'Khách hàng VIP',
                'description' => 'Giảm 50k cho khách hàng VIP',
                'type' => 'fixed',
                'value' => 50000,
                'min_order_amount' => 100000,
                'max_discount' => null,
                'usage_limit' => 100,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
                'is_active' => true,
            ],
            [
                'code' => 'FLASH30',
                'name' => 'Flash Sale 30%',
                'description' => 'Giảm 30% trong ngày flash sale',
                'type' => 'percentage',
                'value' => 30,
                'min_order_amount' => 400000,
                'max_discount' => 300000,
                'usage_limit' => 200,
                'used_count' => 0,
                'starts_at' => now()->addDays(1),
                'expires_at' => now()->addDays(2),
                'is_active' => true,
            ],
            [
                'code' => 'EXPIRED',
                'name' => 'Mã đã hết hạn',
                'description' => 'Mã giảm giá đã hết hạn để test',
                'type' => 'percentage',
                'value' => 15,
                'min_order_amount' => 200000,
                'max_discount' => 100000,
                'usage_limit' => 50,
                'used_count' => 0,
                'starts_at' => now()->subDays(30),
                'expires_at' => now()->subDays(1),
                'is_active' => true,
            ],
            [
                'code' => 'INACTIVE',
                'name' => 'Mã bị vô hiệu hóa',
                'description' => 'Mã giảm giá bị vô hiệu hóa để test',
                'type' => 'fixed',
                'value' => 25000,
                'min_order_amount' => 150000,
                'max_discount' => null,
                'usage_limit' => 100,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
                'is_active' => false,
            ],
            [
                'code' => 'LIMITED',
                'name' => 'Mã giới hạn sử dụng',
                'description' => 'Mã giảm giá có giới hạn sử dụng thấp',
                'type' => 'percentage',
                'value' => 25,
                'min_order_amount' => 300000,
                'max_discount' => 150000,
                'usage_limit' => 5,
                'used_count' => 3,
                'starts_at' => now(),
                'expires_at' => now()->addDays(15),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::create($couponData);
        }
    }
}