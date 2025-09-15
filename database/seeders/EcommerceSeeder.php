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

        // Create user for supplier first
        $supplierUser = User::firstOrCreate(
            ['email' => 'supplier@construction.com'],
            [
                'name' => 'مورد مواد البناء الأول',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '+1122334455',
                'address' => 'مدينة الرياض، المملكة العربية السعودية',
                'email_verified_at' => now(),
            ]
        );

        // Create suppliers only if they don't exist  
        $supplier = Supplier::firstOrCreate(
            ['user_id' => $supplierUser->id],
            [
                'rating' => 4.5,
                'certifications' => json_encode([]),
            ]
        );

        // Create categories only if they don't exist
        $cementCategory = Category::firstOrCreate(
            ['name_ar' => 'أسمنت ومواد ربط'],
            [
                'name_en' => 'Cement & Binding Materials',
                'description_ar' => 'جميع أنواع الأسمنت ومواد الربط للبناء',
                'description_en' => 'All types of cement and binding materials for construction',
                'status' => 'active',
                'parent_id' => null,
                'sort_order' => 1,
            ]
        );

        $steelCategory = Category::firstOrCreate(
            ['name_ar' => 'حديد ومعادن'],
            [
                'name_en' => 'Steel & Metals',
                'description_ar' => 'حديد التسليح والمعادن المختلفة',
                'description_en' => 'Reinforcement steel and various metals',
                'status' => 'active',
                'parent_id' => null,
                'sort_order' => 2,
            ]
        );

        $bricksCategory = Category::firstOrCreate(
            ['name_ar' => 'طوب ومواد البناء'],
            [
                'name_en' => 'Bricks & Building Materials',
                'description_ar' => 'طوب أحمر وأبيض ومواد البناء الأساسية',
                'description_en' => 'Red and white bricks and basic building materials',
                'status' => 'active',
                'parent_id' => null,
                'sort_order' => 3,
            ]
        );

        $toolsCategory = Category::firstOrCreate(
            ['name_ar' => 'أدوات وعُدد'],
            [
                'name_en' => 'Tools & Equipment',
                'description_ar' => 'أدوات يدوية وكهربائية للبناء',
                'description_en' => 'Manual and electric tools for construction',
                'status' => 'active',
                'parent_id' => null,
                'sort_order' => 4,
            ]
        );

        $aggregatesCategory = Category::firstOrCreate(
            ['name_ar' => 'ركام ورمل'],
            [
                'name_en' => 'Aggregates & Sand',
                'description_ar' => 'رمل وحصى وركام للخرسانة',
                'description_en' => 'Sand, gravel and aggregates for concrete',
                'status' => 'active',
                'parent_id' => null,
                'sort_order' => 5,
            ]
        );

        // Create products only if they don't exist
        $products = [
            [
                'name_ar' => 'أسمنت بورتلاند 50 كيلو',
                'name_en' => 'Portland Cement 50kg',
                'description_ar' => 'أسمنت بورتلاند عالي الجودة للاستخدامات العامة في البناء',
                'description_en' => 'High-quality Portland cement for general construction use',
                'sku' => 'CEMENT-PORT-50',
                'price' => 25.00,
                'category_id' => $cementCategory->id,
                'supplier_id' => $supplier->id,
                'stock' => 1000,
            ],
            [
                'name_ar' => 'حديد تسليح 12 مم',
                'name_en' => 'Steel Rebar 12mm',
                'description_ar' => 'حديد تسليح عالي الجودة للخرسانة المسلحة',
                'description_en' => 'High-grade steel reinforcement bars for reinforced concrete',
                'sku' => 'REBAR-12MM',
                'price' => 45.50,
                'category_id' => $steelCategory->id,
                'supplier_id' => $supplier->id,
                'stock' => 500,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }

        // Create sample notifications only if they don't exist
        $notifications = [
            [
                'type' => 'order',
                'user_id' => $adminUser->id,
                'message' => 'تم وضع طلب جديد بقيمة 150 ج.م',
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'stock',
                'user_id' => $adminUser->id,
                'message' => 'مخزون المنتج حديد تسليح 12 مم أصبح أقل من 10 قطع',
                'read_at' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'type' => 'offer',
                'user_id' => $customerUser->id,
                'message' => 'عرض خاص: خصم 20% على جميع أدوات البناء',
                'read_at' => now(),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
        ];

        foreach ($notifications as $notificationData) {
            // Check if notification doesn't exist before creating
            $exists = DB::table('notifications')
                ->where('user_id', $notificationData['user_id'])
                ->where('type', $notificationData['type'])
                ->where('created_at', $notificationData['created_at'])
                ->exists();

            if (!$exists) {
                DB::table('notifications')->insert($notificationData);
            }
        }

        // Create sample cost calculations
        $costCalculations = [
            [
                'user_id' => $customerUser->id,
                'area' => 100.00,
                'materials' => json_encode([
                    ['product_id' => 1, 'quantity' => 10],
                    ['product_id' => 2, 'quantity' => 5]
                ]),
                'total_cost' => 350.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $customerUser->id,
                'area' => 50.00,
                'materials' => json_encode([
                    ['product_id' => 1, 'quantity' => 5]
                ]),
                'total_cost' => 125.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($costCalculations as $costData) {
            CostCalculation::firstOrCreate(
                ['user_id' => $costData['user_id'], 'area' => $costData['area']],
                $costData
            );
        }

        echo "🎉 تم إنشاء البيانات الأساسية بنجاح\n";
        echo "👑 Admin: admin@construction.com | password\n";
        echo "👤 Customer: customer@construction.com | password\n";
    }
} 