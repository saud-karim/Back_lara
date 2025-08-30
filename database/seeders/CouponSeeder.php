<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run()
    {
        // حذف الكوبونات الموجودة (للتطوير فقط)
        Coupon::truncate();

        $coupons = [
            [
                'code' => 'SAVE10',
                'type' => 'percentage',
                'value' => 10.00,
                'min_order_amount' => 100.00,
                'max_discount_amount' => 50.00,
                'usage_limit' => 1000,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(3),
                'status' => 'active',
            ],
            [
                'code' => 'DISCOUNT20',
                'type' => 'percentage',
                'value' => 20.00,
                'min_order_amount' => 200.00,
                'max_discount_amount' => 100.00,
                'usage_limit' => 500,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(6),
                'status' => 'active',
            ],
            [
                'code' => 'FLAT50',
                'type' => 'fixed',
                'value' => 50.00,
                'min_order_amount' => 300.00,
                'max_discount_amount' => null,
                'usage_limit' => 200,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addYears(1),
                'status' => 'active',
            ],
            [
                'code' => 'WELCOME25',
                'type' => 'percentage',
                'value' => 25.00,
                'min_order_amount' => 150.00,
                'max_discount_amount' => 75.00,
                'usage_limit' => 100,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(2),
                'status' => 'active',
            ],
            [
                'code' => 'SUMMER15',
                'type' => 'percentage',
                'value' => 15.00,
                'min_order_amount' => 250.00,
                'max_discount_amount' => 60.00,
                'usage_limit' => 300,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(4),
                'status' => 'active',
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }

        $this->command->info('🎫 Test coupons created successfully!');
        $this->command->info('   💰 SAVE10 - 10% off orders over 100 EGP');
        $this->command->info('   💰 DISCOUNT20 - 20% off orders over 200 EGP');  
        $this->command->info('   💰 FLAT50 - 50 EGP off orders over 300 EGP');
        $this->command->info('   💰 WELCOME25 - 25% off orders over 150 EGP');
        $this->command->info('   💰 SUMMER15 - 15% off orders over 250 EGP');
    }
} 