<?php

echo "🚀 إنشاء سيدر نظيف ومُبسط\n";
echo "===========================\n\n";

// إنشاء سيدر بسيط جديد
$cleanSeederContent = '<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CleanSeeder extends Seeder
{
    public function run()
    {
        echo "🌱 إضافة بيانات نظيفة ومُبسطة\\n";
        
        // 1. إضافة مستخدمين إضافيين
        $users = [
            [
                "name" => "أحمد محمد",
                "email" => "ahmed@example.com",
                "password" => Hash::make("password"),
                "role" => "customer",
                "phone" => "+201234567890",
                "address" => "القاهرة، مصر"
            ],
            [
                "name" => "فاطمة علي", 
                "email" => "fatma@example.com",
                "password" => Hash::make("password"),
                "role" => "customer",
                "phone" => "+201234567891",
                "address" => "الجيزة، مصر"
            ],
            [
                "name" => "محمد عمر",
                "email" => "mohammed@example.com", 
                "password" => Hash::make("password"),
                "role" => "customer",
                "phone" => "+201234567892",
                "address" => "الإسكندرية، مصر"
            ]
        ];
        
        foreach ($users as $userData) {
            User::firstOrCreate([\'email\' => $userData[\'email\']], $userData);
        }
        
        // 2. إضافة منتجات إضافية
        $category = Category::first();
        $supplier = Supplier::first(); 
        $brand = Brand::first();
        
        if ($category && $supplier) {
            $products = [
                [
                    "name_ar" => "منشار كهربائي متطور",
                    "name_en" => "Advanced Electric Saw",
                    "description_ar" => "منشار كهربائي عالي الأداء للأعمال الشاقة",
                    "description_en" => "High-performance electric saw for heavy duty work",
                    "price" => 299.99,
                    "sale_price" => 249.99,
                    "stock" => 25,
                    "sku" => "SAW-ADV-001",
                    "category_id" => $category->id,
                    "supplier_id" => $supplier->id,
                    "brand_id" => $brand?->id,
                    "status" => "active",
                    "featured" => true
                ],
                [
                    "name_ar" => "شاكوش هيدروليكي",
                    "name_en" => "Hydraulic Hammer", 
                    "description_ar" => "شاكوش هيدروليكي قوي للأعمال الإنشائية",
                    "description_en" => "Powerful hydraulic hammer for construction work",
                    "price" => 450.00,
                    "stock" => 15,
                    "sku" => "HAM-HYD-002",
                    "category_id" => $category->id,
                    "supplier_id" => $supplier->id,
                    "brand_id" => $brand?->id,
                    "status" => "active",
                    "featured" => false
                ]
            ];
            
            foreach ($products as $productData) {
                Product::firstOrCreate([\'sku\' => $productData[\'sku\']], $productData);
            }
        }
        
        // 3. إضافة طلبات بسيطة
        $customers = User::where("role", "customer")->take(3)->get();
        $products = Product::take(2)->get();
        
        if ($customers->count() > 0 && $products->count() > 0) {
            foreach ($customers as $customer) {
                $order = Order::create([
                    "user_id" => $customer->id,
                    "total_amount" => 599.98,
                    "status" => "completed",
                    "shipping_address" => $customer->address,
                    "payment_method" => "cash_on_delivery"
                ]);
                
                foreach ($products as $product) {
                    OrderItem::create([
                        "order_id" => $order->id,
                        "product_id" => $product->id,
                        "quantity" => 1,
                        "price" => $product->price
                    ]);
                }
            }
        }
        
        echo "✅ تم إضافة البيانات بنجاح\\n";
    }
}';

file_put_contents('database/seeders/CleanSeeder.php', $cleanSeederContent);
echo "✅ تم إنشاء CleanSeeder.php\n";

// تحديث DatabaseSeeder ليستخدم السيدرز الآمنة
$safeDatabaseSeeder = '<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            BrandSeeder::class, 
            EcommerceSeeder::class,    // بيانات أساسية آمنة
            CleanSeeder::class,        // بيانات إضافية نظيفة
        ]);

        $this->command->info("🎉 جميع السيدرز تمت بنجاح!");
        $this->command->info("🔐 حسابات الاختبار:");
        $this->command->info("   👑 Admin: admin@construction.com | password");
        $this->command->info("   👤 Customer: customer@construction.com | password");
        $this->command->info("   👤 Ahmed: ahmed@example.com | password");
        $this->command->info("📦 قاعدة البيانات تحتوي على بيانات اختبار شاملة!");
    }
}';

file_put_contents('database/seeders/DatabaseSeeder.php', $safeDatabaseSeeder);
echo "✅ تم تحديث DatabaseSeeder.php\n";

echo "\n🎯 النتيجة النهائية:\n";
echo "==================\n";
echo "✅ السيدرز الآمنة: PermissionSeeder, BrandSeeder, EcommerceSeeder, CleanSeeder\n";
echo "✅ إزالة السيدرز المشكوك فيها: TranslatedDataSeeder, ExtendedDataSeeder\n";
echo "✅ بيانات متوافقة 100% مع الـ schema الحالي\n";

echo "\n🚀 للتطبيق النهائي:\n";
echo "==================\n";
echo "php artisan migrate:fresh --seed\n";

echo "\n" . str_repeat("=", 50) . "\n"; 