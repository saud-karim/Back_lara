<?php

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

class CleanSeeder extends Seeder
{
    public function run()
    {
        echo "🌱 إضافة بيانات نظيفة ومُبسطة\n";
        
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
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }
        
        // 2. إضافة منتجات إضافية (بدون طلبات لتجنب مشاكل orders)
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
                ],
                [
                    "name_ar" => "أدوات قياس دقيقة",
                    "name_en" => "Precision Measuring Tools", 
                    "description_ar" => "مجموعة أدوات قياس عالية الدقة للمهندسين",
                    "description_en" => "High-precision measuring tools set for engineers",
                    "price" => 180.00,
                    "stock" => 40,
                    "sku" => "MSR-PRE-003",
                    "category_id" => $category->id,
                    "supplier_id" => $supplier->id,
                    "brand_id" => $brand?->id,
                    "status" => "active",
                    "featured" => true
                ]
            ];
            
            foreach ($products as $productData) {
                Product::firstOrCreate(['sku' => $productData['sku']], $productData);
            }
        }
        
        echo "✅ تم إضافة البيانات بنجاح (منتجات ومستخدمين)\n";
        echo "ℹ️  تم تجنب إضافة طلبات لتجنب مشاكل orders schema\n";
    }
}