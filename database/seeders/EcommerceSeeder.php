<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CostCalculation;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EcommerceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create users only if they don't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@construction.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'address' => '123 Admin Street, City',
                'phone' => '+1234567890',
                'email_verified_at' => now(),
            ]
        );

        $customerUser = User::firstOrCreate(
            ['email' => 'customer@construction.com'],
            [
                'name' => 'Customer User',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'address' => '456 Customer Ave, Town',
                'phone' => '+0987654321',
                'email_verified_at' => now(),
            ]
        );

        $supplierUser = User::firstOrCreate(
            ['email' => 'supplier@construction.com'],
            [
                'name' => 'Supplier User',
                'password' => Hash::make('password'),
                'role' => 'supplier',
                'address' => '789 Supplier Blvd, Village',
                'phone' => '+1122334455',
                'email_verified_at' => now(),
            ]
        );

        // Assign roles if they don't have them
        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole('admin');
        }
        if (!$customerUser->hasRole('customer')) {
            $customerUser->assignRole('customer');
        }
        if (!$supplierUser->hasRole('supplier')) {
            $supplierUser->assignRole('supplier');
        }

        // Create categories
        $categories = [
            [
                'name_ar' => 'المواد الخام',
                'name_en' => 'Raw Materials', 
                'description_ar' => 'المواد الأساسية للبناء والتشييد',
                'description_en' => 'Basic materials for construction and building',
                'parent_id' => null,
                'status' => 'active',
                'sort_order' => 1
            ],
            [
                'name_ar' => 'الأدوات الكهربائية',
                'name_en' => 'Power Tools',
                'description_ar' => 'أدوات كهربائية احترافية للبناء',
                'description_en' => 'Professional power tools for construction',
                'parent_id' => null,
                'status' => 'active',
                'sort_order' => 2
            ],
            [
                'name_ar' => 'الأدوات اليدوية',
                'name_en' => 'Hand Tools',
                'description_ar' => 'أدوات يدوية تقليدية للاستخدام المهني',
                'description_en' => 'Traditional hand tools for professional use',
                'parent_id' => null,
                'status' => 'active',
                'sort_order' => 3
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(['name_en' => $categoryData['name_en']], $categoryData);
        }

        // Create subcategories
        $rawMaterialsCategory = Category::where('name_en', 'Raw Materials')->first();
        $subcategories = [
            [
                'name_ar' => 'الأسمنت',
                'name_en' => 'Cement',
                'description_ar' => 'أسمنت عالي الجودة لأعمال البناء',
                'description_en' => 'High-quality cement for construction work',
                'parent_id' => $rawMaterialsCategory->id,
                'status' => 'active',
                'sort_order' => 1
            ],
            [
                'name_ar' => 'الحديد',
                'name_en' => 'Steel',
                'description_ar' => 'حديد التسليح والإنشاءات',
                'description_en' => 'Reinforcing steel for construction',
                'parent_id' => $rawMaterialsCategory->id,
                'status' => 'active',
                'sort_order' => 2
            ],
            [
                'name_ar' => 'الطوب',
                'name_en' => 'Bricks',
                'description_ar' => 'طوب البناء بأنواعه المختلفة',
                'description_en' => 'Various types of building bricks',
                'parent_id' => $rawMaterialsCategory->id,
                'status' => 'active',
                'sort_order' => 3
            ]
        ];

        foreach ($subcategories as $subcategoryData) {
            Category::firstOrCreate(['name_en' => $subcategoryData['name_en']], $subcategoryData);
        }

        // Create supplier
        $supplier = Supplier::firstOrCreate(
            ['user_id' => $supplierUser->id],
            [
                'name_ar' => 'مورد مواد البناء الأول',
                'name_en' => 'Construction Materials Supplier 1',
                'description_ar' => 'مورد رائد في مواد البناء والإنشاءات',
                'description_en' => 'Leading supplier of building and construction materials',
                'email' => 'supplier@construction.com',
                'phone' => '+1122334455',
                'rating' => 4.5,
                'certifications' => json_encode([
                    'ISO 9001:2015',
                    'OHSAS 18001:2007',
                    'Environmental Management Certificate'
                ]),
            ]
        );

        // Create products
        $cementCategory = Category::where('name_en', 'Cement')->first();
        $steelCategory = Category::where('name_en', 'Steel')->first();

        $products = [
            [
                'name_ar' => 'أسمنت بورتلاند 50 كيلو',
                'name_en' => 'Portland Cement 50kg',
                'description_ar' => 'أسمنت بورتلاند عالي الجودة للبناء والإنشاءات',
                'description_en' => 'High-quality Portland cement for construction',
                'price' => 25.00,
                'category_id' => $cementCategory->id,
                'supplier_id' => $supplier->id,
                'stock' => 1000,
                'images' => json_encode([
                    'https://example.com/images/portland-cement-50kg_1.jpg',
                    'https://example.com/images/portland-cement-50kg_2.jpg'
                ]),
            ],
            [
                'name_ar' => 'حديد تسليح 12 مم',
                'name_en' => 'Steel Rebar 12mm',
                'description_ar' => 'حديد تسليح عالي الجودة للخرسانة المسلحة',
                'description_en' => 'High-grade steel reinforcement bars for reinforced concrete',
                'price' => 45.50,
                'category_id' => $steelCategory->id,
                'supplier_id' => $supplier->id,
                'stock' => 500,
                'images' => json_encode([
                    'https://example.com/images/steel-rebar-12mm_1.jpg',
                    'https://example.com/images/steel-rebar-12mm_2.jpg'
                ]),
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(['name_en' => $productData['name_en']], $productData);
        }

        // Create orders
        $product1 = Product::where('name_en', 'Portland Cement 50kg')->first();
        $product2 = Product::where('name_en', 'Steel Rebar 12mm')->first();

        $order = Order::firstOrCreate(
            ['user_id' => $customerUser->id],
            [
                'status' => 'pending',
                'subtotal' => 0,
                'total_amount' => 0, // Will be calculated
                'shipping_address' => json_encode([
                    'street' => '456 Customer Ave',
                    'city' => 'Town',
                    'country' => 'Egypt'
                ]),
                'payment_method' => 'cash_on_delivery',
            ]
        );

        // Create order items if order was just created
        if ($order->wasRecentlyCreated) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product1->id,
                'quantity' => 10,
                'unit_price' => $product1->price,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product2->id,
                'quantity' => 5,
                'unit_price' => $product2->price,
            ]);

            // Calculate total amount
            $totalAmount = ($product1->price * 10) + ($product2->price * 5);
            $order->update([
                'subtotal' => $totalAmount,
                'total_amount' => $totalAmount
            ]);
        }

        // Create shipment
        if (!Shipment::where('order_id', $order->id)->exists()) {
            Shipment::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'tracking_number' => 'TRACK' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
                'estimated_delivery' => now()->addDays(7),
            ]);
        }

        // Create notifications
        $notifications = [
            [
                'user_id' => $customerUser->id,
                'type' => 'order',
                'title_ar' => 'تأكيد الطلب',
                'title_en' => 'Order Confirmation',
                'message_ar' => 'تم وضع طلبك بنجاح وجاري المراجعة',
                'message_en' => 'Your order has been placed successfully and is under review',
                'read_at' => null,
            ],
        ];

        foreach ($notifications as $notificationData) {
            $existingNotification = DB::table('notifications')
                ->where('user_id', $notificationData['user_id'])
                ->where('type', $notificationData['type'])
                ->where('message_en', $notificationData['message_en'])
                ->first();
                
            if (!$existingNotification) {
                DB::table('notifications')->insert($notificationData);
            }
        }

        // Create cost calculation
        CostCalculation::firstOrCreate(
            ['user_id' => $customerUser->id],
            [
                'area' => 100.00,
                'materials' => [
                    [
                        'product_id' => $product1->id,
                        'quantity' => 10,
                        'unit_price' => $product1->price,
                        'item_cost' => $product1->price * 10,
                    ],
                    [
                        'product_id' => $product2->id,
                        'quantity' => 5,
                        'unit_price' => $product2->price,
                        'item_cost' => $product2->price * 5,
                    ],
                ],
                'total_cost' => ($product1->price * 10) + ($product2->price * 5),
            ]
        );
    }
} 