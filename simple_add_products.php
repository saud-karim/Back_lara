<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "⚡ إضافة سريعة للموردين والمنتجات\n";
echo "=================================\n\n";

try {
    
    // 1. إضافة موردين بسيطين
    echo "1️⃣ إضافة الموردين:\n";
    
    $admin = DB::table('users')->where('role', 'admin')->first();
    if (!$admin) {
        echo "❌ لا يوجد مدير\n";
        exit;
    }
    
    $currentSuppliers = DB::table('suppliers')->count();
    if ($currentSuppliers == 0) {
        
        $suppliers = [
            [
                'user_id' => $admin->id,
                'name' => 'مورد أساسي',
                'name_ar' => 'مورد أساسي',
                'name_en' => 'Basic Supplier',
                'description_ar' => 'مورد مواد البناء',
                'description_en' => 'Construction materials supplier',
                'contact_person' => 'مدير المبيعات',
                'email' => 'supplier1@example.com',
                'phone' => '01234567890',
                'address' => 'القاهرة',
                'city' => 'القاهرة',
                'country' => 'مصر',
                'status' => 'active',
                'rating' => 4.5,
                'certifications' => json_encode(['ISO 9001']), // JSON format
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => $admin->id,
                'name' => 'مورد الأدوات',
                'name_ar' => 'مورد الأدوات',
                'name_en' => 'Tools Supplier',
                'description_ar' => 'أدوات البناء والكهرباء',
                'description_en' => 'Construction and electrical tools',
                'contact_person' => 'أحمد علي',
                'email' => 'supplier2@example.com',
                'phone' => '01987654321',
                'address' => 'الجيزة',
                'city' => 'الجيزة',
                'country' => 'مصر',
                'status' => 'active',
                'rating' => 4.2,
                'certifications' => json_encode(['CE']), // JSON format
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => $admin->id,
                'name' => 'مورد ممتاز',
                'name_ar' => 'مورد ممتاز',
                'name_en' => 'Premium Supplier',
                'description_ar' => 'خامات عالية الجودة',
                'description_en' => 'High quality materials',
                'contact_person' => 'سارة محمد',
                'email' => 'supplier3@example.com',
                'phone' => '01555666777',
                'address' => 'الإسكندرية',
                'city' => 'الإسكندرية',
                'country' => 'مصر',
                'status' => 'active',
                'rating' => 4.8,
                'certifications' => json_encode(['ISO 9001', 'ISO 14001']), // JSON format
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        
        foreach ($suppliers as $index => $supplier) {
            try {
                $id = DB::table('suppliers')->insertGetId($supplier);
                echo "   ✅ {$supplier['name_ar']} (ID: {$id})\n";
            } catch (Exception $e) {
                echo "   ❌ فشل: {$supplier['name_ar']} - " . $e->getMessage() . "\n";
            }
        }
        echo "\n";
    } else {
        echo "   ✅ الموردين موجودين ({$currentSuppliers})\n\n";
    }
    
    // 2. إضافة منتجات أساسية
    echo "2️⃣ إضافة المنتجات:\n";
    
    $currentProducts = DB::table('products')->count();
    
    if ($currentProducts < 30) {
        
        // جلب البيانات المطلوبة
        $category = DB::table('categories')->first();
        $supplier = DB::table('suppliers')->first();
        $brand = DB::table('brands')->first();
        
        if (!$category || !$supplier || !$brand) {
            echo "❌ البيانات الأساسية غير مكتملة\n";
            exit;
        }
        
        echo "   📊 سيتم ربط المنتجات بـ:\n";
        echo "   📁 الفئة: {$category->name_ar}\n";
        echo "   🏢 المورد: {$supplier->name_ar}\n";
        echo "   🏷️ البراند: {$brand->name_ar}\n\n";
        
        $products = [
            // مواد البناء
            ['name_ar' => 'حديد تسليح 8 مم', 'name_en' => '8mm Rebar Steel', 'price' => 150, 'stock' => 400],
            ['name_ar' => 'حديد تسليح 10 مم', 'name_en' => '10mm Rebar Steel', 'price' => 180, 'stock' => 350],
            ['name_ar' => 'حديد تسليح 12 مم', 'name_en' => '12mm Rebar Steel', 'price' => 220, 'stock' => 300],
            ['name_ar' => 'حديد تسليح 14 مم', 'name_en' => '14mm Rebar Steel', 'price' => 280, 'stock' => 250],
            ['name_ar' => 'حديد تسليح 16 مم', 'name_en' => '16mm Rebar Steel', 'price' => 320, 'stock' => 200],
            
            // أسمنت
            ['name_ar' => 'أسمنت بورتلاند 50 كج', 'name_en' => 'Portland Cement 50kg', 'price' => 160, 'stock' => 800],
            ['name_ar' => 'أسمنت بورتلاند 25 كج', 'name_en' => 'Portland Cement 25kg', 'price' => 95, 'stock' => 1000],
            ['name_ar' => 'أسمنت أبيض 25 كج', 'name_en' => 'White Cement 25kg', 'price' => 110, 'stock' => 600],
            
            // أدوات كهربائية
            ['name_ar' => 'شنيور بوش 500 وات', 'name_en' => 'Bosch Drill 500W', 'price' => 850, 'stock' => 25],
            ['name_ar' => 'شنيور ماكيتا لاسلكي', 'name_en' => 'Makita Cordless Drill', 'price' => 1200, 'stock' => 15],
            ['name_ar' => 'منشار دائري 184 مم', 'name_en' => 'Circular Saw 184mm', 'price' => 950, 'stock' => 20],
            ['name_ar' => 'صاروخ كهربائي 115 مم', 'name_en' => 'Electric Grinder 115mm', 'price' => 650, 'stock' => 30],
            
            // أدوات يدوية
            ['name_ar' => 'مطرقة 500 جرام', 'name_en' => '500g Hammer', 'price' => 65, 'stock' => 80],
            ['name_ar' => 'مطرقة 1 كيلو', 'name_en' => '1kg Hammer', 'price' => 95, 'stock' => 60],
            ['name_ar' => 'مجموعة مفكات 12 قطعة', 'name_en' => '12-piece Screwdriver Set', 'price' => 120, 'stock' => 45],
            ['name_ar' => 'كماشة 8 بوصة', 'name_en' => '8-inch Pliers', 'price' => 85, 'stock' => 70],
            
            // كهرباء
            ['name_ar' => 'كابل كهرباء 1.5 مم', 'name_en' => '1.5mm Electric Cable', 'price' => 12, 'stock' => 5000],
            ['name_ar' => 'كابل كهرباء 2.5 مم', 'name_en' => '2.5mm Electric Cable', 'price' => 18, 'stock' => 3000],
            ['name_ar' => 'لمبة LED 9 وات', 'name_en' => '9W LED Bulb', 'price' => 35, 'stock' => 200],
            ['name_ar' => 'لمبة LED 12 وات', 'name_en' => '12W LED Bulb', 'price' => 45, 'stock' => 180],
            ['name_ar' => 'كشاف LED 30 وات', 'name_en' => '30W LED Floodlight', 'price' => 180, 'stock' => 50],
            
            // سباكة
            ['name_ar' => 'ماسورة PVC 2 بوصة', 'name_en' => '2-inch PVC Pipe', 'price' => 25, 'stock' => 400],
            ['name_ar' => 'ماسورة PVC 3 بوصة', 'name_en' => '3-inch PVC Pipe', 'price' => 35, 'stock' => 350],
            ['name_ar' => 'كوع PVC 90 درجة', 'name_en' => '90° PVC Elbow', 'price' => 12, 'stock' => 400],
            ['name_ar' => 'صنبور مطبخ نحاس', 'name_en' => 'Brass Kitchen Faucet', 'price' => 180, 'stock' => 60],
            
            // دهانات
            ['name_ar' => 'دهان أبيض 4 لتر', 'name_en' => 'White Paint 4L', 'price' => 120, 'stock' => 80],
            ['name_ar' => 'دهان أبيض 20 لتر', 'name_en' => 'White Paint 20L', 'price' => 550, 'stock' => 30],
            ['name_ar' => 'برايمر للحوائط', 'name_en' => 'Wall Primer', 'price' => 85, 'stock' => 70],
            
            // سيراميك
            ['name_ar' => 'سيراميك 30×30', 'name_en' => '30x30 Ceramic', 'price' => 35, 'stock' => 1500],
            ['name_ar' => 'سيراميك 25×25', 'name_en' => '25x25 Ceramic', 'price' => 25, 'stock' => 2000],
            ['name_ar' => 'بورسلين 60×60', 'name_en' => '60x60 Porcelain', 'price' => 85, 'stock' => 800],
            
            // أمان
            ['name_ar' => 'خوذة أمان بيضاء', 'name_en' => 'White Safety Helmet', 'price' => 35, 'stock' => 150],
            ['name_ar' => 'جوانتي أمان', 'name_en' => 'Safety Gloves', 'price' => 25, 'stock' => 200],
            ['name_ar' => 'نظارة أمان', 'name_en' => 'Safety Glasses', 'price' => 20, 'stock' => 180],
            
            // أدوات قياس
            ['name_ar' => 'شريط قياس 5 متر', 'name_en' => '5m Measuring Tape', 'price' => 25, 'stock' => 150],
            ['name_ar' => 'ميزان مياه 60 سم', 'name_en' => '60cm Spirit Level', 'price' => 75, 'stock' => 60],
            
            // مواد لاصقة
            ['name_ar' => 'سيلكون شفاف', 'name_en' => 'Clear Silicone', 'price' => 15, 'stock' => 300],
            ['name_ar' => 'لاصق سيراميك 25 كج', 'name_en' => 'Ceramic Adhesive 25kg', 'price' => 85, 'stock' => 120],
            
            // مسامير ومواد تثبيت
            ['name_ar' => 'مسامير خرسانة 6×40', 'name_en' => '6x40 Concrete Screws', 'price' => 2.5, 'stock' => 10000],
            ['name_ar' => 'مسامير خرسانة 8×60', 'name_en' => '8x60 Concrete Screws', 'price' => 3.5, 'stock' => 8000],
            ['name_ar' => 'براغي خشب 4×30', 'name_en' => '4x30 Wood Screws', 'price' => 1.5, 'stock' => 15000],
            
            // منتجات متنوعة
            ['name_ar' => 'رمل أبيض للبناء', 'name_en' => 'White Construction Sand', 'price' => 45, 'stock' => 2000],
            ['name_ar' => 'زلط للخرسانة', 'name_en' => 'Concrete Gravel', 'price' => 35, 'stock' => 1500],
            ['name_ar' => 'طوب أحمر عادي', 'name_en' => 'Regular Red Brick', 'price' => 1.2, 'stock' => 50000],
            ['name_ar' => 'بلاط أسمنتي 20×20', 'name_en' => '20x20 Cement Tile', 'price' => 12, 'stock' => 3000],
            ['name_ar' => 'عازل مائي للأسطح', 'name_en' => 'Roof Waterproofing', 'price' => 220, 'stock' => 40],
            ['name_ar' => 'شريط LED 5 متر', 'name_en' => '5m LED Strip', 'price' => 120, 'stock' => 80],
        ];
        
        $added = 0;
        
        foreach ($products as $index => $product) {
            try {
                $sku = 'PROD-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
                $featured = $index < 8; // أول 8 منتجات مميزة
                $salePrice = null;
                
                // 25% من المنتجات بخصم
                if (rand(1, 4) == 1) {
                    $salePrice = $product['price'] + ($product['price'] * 0.20);
                }
                
                DB::table('products')->insert([
                    'name_ar' => $product['name_ar'],
                    'name_en' => $product['name_en'],
                    'description_ar' => "وصف {$product['name_ar']} - منتج عالي الجودة مناسب لجميع أعمال البناء والتشييد.",
                    'description_en' => "Description of {$product['name_en']} - high quality product suitable for all construction work.",
                    'sku' => $sku,
                    'price' => $product['price'],
                    'sale_price' => $salePrice,
                    'status' => 'active',
                    'featured' => $featured,
                    'sort_order' => $index + 1,
                    'specifications' => '[]',
                    'features' => '[]',
                    'weight' => rand(10, 500) / 10,
                    'dimensions' => 'Standard',
                    'category_id' => $category->id,
                    'supplier_id' => $supplier->id,
                    'brand_id' => $brand->id,
                    'stock' => $product['stock'],
                    'images' => '[]',
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()
                ]);
                
                $added++;
                
                if ($added % 10 == 0) {
                    echo "   ✅ {$added} منتج تم إضافته...\n";
                }
                
            } catch (Exception $e) {
                echo "   ⚠️ تخطي: {$product['name_ar']}\n";
            }
        }
        
        echo "   🎉 تم إضافة {$added} منتج!\n\n";
        
    } else {
        echo "   ✅ المنتجات موجودة ({$currentProducts})\n\n";
    }
    
    // 3. الإحصائيات النهائية
    echo "3️⃣ النتائج النهائية:\n";
    $finalStats = [
        'المنتجات' => DB::table('products')->count(),
        'النشطة' => DB::table('products')->where('status', 'active')->count(),
        'المميزة' => DB::table('products')->where('featured', true)->count(),
        'الموردين' => DB::table('suppliers')->count(),
        'الفئات' => DB::table('categories')->count(),
        'البراندات' => DB::table('brands')->count()
    ];
    
    foreach ($finalStats as $key => $value) {
        echo "   📊 {$key}: {$value}\n";
    }
    
    echo "\n=================================\n";
    echo "🎉 **اكتملت العملية بنجاح!**\n\n";
    
    echo "🔑 **بيانات تسجيل الدخول:**\n\n";
    
    echo "👨‍💼 **المدير:**\n";
    echo "   📧 Email: admin@construction.com\n";
    echo "   🔐 Password: admin123\n\n";
    
    $customer = DB::table('users')->where('role', 'customer')->first();
    if ($customer) {
        echo "🛒 **عميل للاختبار:**\n";
        echo "   📧 Email: {$customer->email}\n";
        echo "   🔐 Password: password123\n\n";
    }
    
    echo "🌐 **روابط للاختبار:**\n";
    echo "   🔗 Admin Products: GET http://127.0.0.1:8000/api/v1/admin/products\n";
    echo "   🔗 Public Products: GET http://127.0.0.1:8000/api/v1/products\n";
    echo "   🔗 Product Stats: GET http://127.0.0.1:8000/api/v1/admin/products/stats\n\n";
    
    echo "🚀 **الآن يمكنك رؤية المنتجات!** 🚀\n";

} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "📂 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
} 