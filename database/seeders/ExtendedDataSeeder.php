<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\Notification;
use App\Models\CostCalculation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ExtendedDataSeeder extends Seeder
{
    public function run()
    {
        // إنشاء المزيد من المستخدمين
        $users = [
            [
                'name' => 'Ahmed Construction Manager',
                'email' => 'ahmed.manager@construction.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'address' => 'New Cairo, Phase 1, Villa 123',
                'phone' => '+201234567891',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Fatma Architect',
                'email' => 'fatma.architect@design.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'address' => 'Zamalek, Cairo, Apartment 45',
                'phone' => '+201234567892',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mohamed Contractor',
                'email' => 'mohamed.contractor@build.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'address' => 'Maadi, Cairo, Building 67',
                'phone' => '+201234567893',
                'email_verified_at' => now(),
            ],
        ];

        $userIds = [];
        foreach ($users as $user) {
            $usr = User::firstOrCreate(['email' => $user['email']], $user);
            $userIds[] = $usr->id;
        }

        // الحصول على البيانات الموجودة
        $existingCategories = Category::all();
        $existingSuppliers = Supplier::all();
        $existingProducts = Product::all();

        // إنشاء بعض المنتجات الإضافية باستخدام الفئات الموجودة
        if ($existingCategories->count() > 0 && $existingSuppliers->count() > 0) {
            $additionalProducts = [
                [
                    'name' => 'Premium Steel Rebar 10mm',
                    'description' => 'High-grade steel reinforcement bars for construction',
                    'price' => 28.50,
                    'category_id' => $existingCategories->first()->id,
                    'supplier_id' => $existingSuppliers->first()->id,
                    'stock' => 2000,
                    'images' => json_encode(['rebar10mm.jpg']),
                ],
                [
                    'name' => 'Construction Sand - 1 Ton',
                    'description' => 'Clean washed sand for concrete and construction',
                    'price' => 45.00,
                    'category_id' => $existingCategories->first()->id,
                    'supplier_id' => $existingSuppliers->first()->id,
                    'stock' => 500,
                    'images' => json_encode(['sand.jpg']),
                ],
                [
                    'name' => 'Red Bricks Pack (100 pieces)',
                    'description' => 'Traditional clay bricks for building walls',
                    'price' => 120.00,
                    'category_id' => $existingCategories->first()->id,
                    'supplier_id' => $existingSuppliers->first()->id,
                    'stock' => 300,
                    'images' => json_encode(['red_bricks.jpg']),
                ],
            ];

            $newProductIds = [];
            foreach ($additionalProducts as $product) {
                $prod = Product::firstOrCreate(
                    ['name' => $product['name']],
                    $product
                );
                $newProductIds[] = $prod->id;
            }
        }

        // إنشاء طلبات بسيطة
        if (count($userIds) > 0 && $existingProducts->count() > 0) {
            $orders = [
            [
                'user_id' => $userIds[0],
                    'subtotal' => 285.00,
                    'total_amount' => 285.00,
                'status' => 'delivered',
                    'payment_method' => 'credit_card',
                    'shipping_address' => json_encode([
                        'street' => 'New Cairo Construction Site',
                        'city' => 'Cairo',
                        'country' => 'Egypt'
                    ]),
                    'created_at' => Carbon::now()->subDays(15),
            ],
            [
                    'user_id' => $userIds[1],
                    'subtotal' => 450.00,
                    'total_amount' => 450.00,
                'status' => 'processing',
                    'payment_method' => 'cash_on_delivery',
                    'shipping_address' => json_encode([
                        'street' => 'Zamalek Project',
                        'city' => 'Cairo',
                        'country' => 'Egypt'
                    ]),
                    'created_at' => Carbon::now()->subDays(5),
            ],
        ];

        $orderIds = [];
        foreach ($orders as $order) {
            $ord = Order::create($order);
            $orderIds[] = $ord->id;
        }

        // إنشاء تفاصيل الطلبات
            if (count($orderIds) > 0) {
                $firstProduct = $existingProducts->first();
                $secondProduct = $existingProducts->skip(1)->first() ?? $firstProduct;

                OrderItem::create([
                    'order_id' => $orderIds[0],
                    'product_id' => $firstProduct->id,
                    'quantity' => 5,
                    'unit_price' => $firstProduct->price,
                ]);

                if (isset($orderIds[1])) {
                    OrderItem::create([
                        'order_id' => $orderIds[1],
                        'product_id' => $secondProduct->id,
                        'quantity' => 3,
                        'unit_price' => $secondProduct->price,
                    ]);
                }
            }
        }

        // إنشاء إشعارات بسيطة
        if (count($userIds) > 0) {
        $notifications = [
            [
                'user_id' => $userIds[0],
                'type' => 'order',
                    'message' => 'Your order has been delivered successfully',
                    'read_at' => null,
                    'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'user_id' => $userIds[1],
                'type' => 'order',
                    'message' => 'Your order is being processed',
                'read_at' => null,
                'created_at' => Carbon::now()->subDays(3),
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }
        }

        $this->command->info('Extended data seeded successfully with simplified approach!');
        $this->command->info('- ' . count($users) . ' additional users created');
        $this->command->info('- Additional products and orders created');
        $this->command->info('- Notifications added');
    }
} 