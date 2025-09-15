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
use App\Models\ProductReview;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ExpandedDataSeeder extends Seeder
{
    public function run()
    {
        echo "🚀 إضافة بيانات موسعة شاملة\n";
        
        // 1. إضافة المزيد من الفئات
        $this->addMoreCategories();
        
        // 2. إضافة المزيد من المنتجات
        $this->addMoreProducts();
        
        // 3. إضافة المزيد من المستخدمين
        $this->addMoreUsers();
        
        // 4. إضافة مراجعات للمنتجات
        $this->addProductReviews();
        
        // 5. إضافة إشعارات متنوعة
        $this->addNotifications();
        
        echo "✅ تم إضافة جميع البيانات الموسعة بنجاح\n";
    }
    
    private function addMoreCategories()
    {
        echo "   📁 إضافة فئات جديدة...\n";
        
        $categories = [
            [
                "name_ar" => "الأدوات الذكية",
                "name_en" => "Smart Tools",
                "description_ar" => "أدوات ذكية ومتطورة مع تقنيات حديثة",
                "description_en" => "Smart and advanced tools with modern technology",
                "status" => "active",
                "sort_order" => 10
            ],
            [
                "name_ar" => "معدات الحدائق",
                "name_en" => "Garden Equipment", 
                "description_ar" => "معدات وأدوات العناية بالحدائق والزراعة",
                "description_en" => "Equipment and tools for garden care and farming",
                "status" => "active",
                "sort_order" => 11
            ],
            [
                "name_ar" => "أدوات السباكة",
                "name_en" => "Plumbing Tools",
                "description_ar" => "أدوات ومعدات السباكة والصحي",
                "description_en" => "Plumbing and sanitary tools and equipment",
                "status" => "active",
                "sort_order" => 12
            ],
            [
                "name_ar" => "معدات اللحام",
                "name_en" => "Welding Equipment",
                "description_ar" => "معدات وأجهزة اللحام المتخصصة",
                "description_en" => "Specialized welding equipment and devices",
                "status" => "active",
                "sort_order" => 13
            ]
        ];
        
        foreach ($categories as $categoryData) {
            Category::firstOrCreate(['name_en' => $categoryData['name_en']], $categoryData);
        }
    }
    
    private function addMoreProducts()
    {
        echo "   🛍️ إضافة منتجات جديدة...\n";
        
        $category = Category::first();
        $supplier = Supplier::first();
        $brands = Brand::all();
        
        if (!$category || !$supplier) return;
        
        $products = [
            [
                "name_ar" => "مفك براغي كهربائي لاسلكي",
                "name_en" => "Cordless Electric Screwdriver",
                "description_ar" => "مفك براغي كهربائي لاسلكي عالي الأداء مع بطارية ليثيوم قابلة للشحن",
                "description_en" => "High-performance cordless electric screwdriver with rechargeable lithium battery",
                "price" => 89.99,
                "sale_price" => 79.99,
                "stock" => 50,
                "sku" => "SCREW-CORD-001",
                "category_id" => $category->id,
                "supplier_id" => $supplier->id,
                "brand_id" => $brands->random()?->id,
                "status" => "active",
                "featured" => true,
                "images" => json_encode(['cordless-screwdriver.jpg']),
                "features" => json_encode([
                    "بطارية ليثيوم 12V",
                    "عزم دوران قابل للتعديل", 
                    "إضاءة LED مدمجة",
                    "شاحن سريع"
                ]),
                "specifications" => json_encode([
                    "الجهد" => "12V",
                    "نوع البطارية" => "ليثيوم أيون",
                    "الوزن" => "0.8 كجم",
                    "وقت الشحن" => "60 دقيقة"
                ])
            ],
            [
                "name_ar" => "طقم مفاتيح إنجليزية احترافي",
                "name_en" => "Professional Wrench Set",
                "description_ar" => "طقم مفاتيح إنجليزية احترافي من الكروم فاناديوم عالي الجودة",
                "description_en" => "Professional chrome vanadium wrench set of high quality",
                "price" => 125.00,
                "stock" => 30,
                "sku" => "WRENCH-PRO-002",
                "category_id" => $category->id,
                "supplier_id" => $supplier->id,
                "brand_id" => $brands->random()?->id,
                "status" => "active",
                "featured" => false,
                "images" => json_encode(['wrench-set.jpg']),
                "features" => json_encode([
                    "كروم فاناديوم عالي الجودة",
                    "15 قطعة مختلفة الأحجام",
                    "حقيبة تنظيم مرفقة",
                    "مقاومة للصدأ"
                ])
            ],
            [
                "name_ar" => "مسدس حرارة صناعي",
                "name_en" => "Industrial Heat Gun",
                "description_ar" => "مسدس حرارة صناعي قوي لأعمال اللحام والتجفيف والتشكيل",
                "description_en" => "Powerful industrial heat gun for welding, drying and shaping work",
                "price" => 65.50,
                "stock" => 20,
                "sku" => "HEAT-GUN-003",
                "category_id" => $category->id,
                "supplier_id" => $supplier->id,
                "brand_id" => $brands->random()?->id,
                "status" => "active",
                "featured" => true,
                "images" => json_encode(['heat-gun.jpg']),
                "specifications" => json_encode([
                    "القوة" => "2000 واط",
                    "درجة الحرارة" => "50-600°C",
                    "تدفق الهواء" => "300-500 لتر/دقيقة"
                ])
            ],
            [
                "name_ar" => "شريط قياس ليزر ذكي",
                "name_en" => "Smart Laser Measuring Tape",
                "description_ar" => "شريط قياس ليزر ذكي مع اتصال بلوتوث وتطبيق جوال",
                "description_en" => "Smart laser measuring tape with Bluetooth connectivity and mobile app",
                "price" => 145.99,
                "sale_price" => 125.99,
                "stock" => 15,
                "sku" => "LASER-TAPE-004",
                "category_id" => $category->id,
                "supplier_id" => $supplier->id,
                "brand_id" => $brands->random()?->id,
                "status" => "active",
                "featured" => true,
                "images" => json_encode(['laser-tape.jpg']),
                "features" => json_encode([
                    "مدى قياس 40 متر",
                    "دقة ±2 مم",
                    "اتصال بلوتوث",
                    "تطبيق جوال مجاني",
                    "شاشة رقمية ملونة"
                ])
            ],
            [
                "name_ar" => "منظف ضغط عالي متنقل",
                "name_en" => "Portable High Pressure Cleaner",
                "description_ar" => "منظف ضغط عالي متنقل للاستخدام المنزلي والتجاري",
                "description_en" => "Portable high pressure cleaner for home and commercial use",
                "price" => 320.00,
                "sale_price" => 285.00,
                "stock" => 12,
                "sku" => "PRESSURE-CLEAN-005",
                "category_id" => $category->id,
                "supplier_id" => $supplier->id,
                "brand_id" => $brands->random()?->id,
                "status" => "active",
                "featured" => false,
                "images" => json_encode(['pressure-cleaner.jpg']),
                "specifications" => json_encode([
                    "ضغط الماء" => "150 بار",
                    "تدفق الماء" => "6.5 لتر/دقيقة",
                    "القوة" => "1800 واط",
                    "خزان المنظف" => "1 لتر"
                ])
            ]
        ];
        
        foreach ($products as $productData) {
            Product::firstOrCreate(['sku' => $productData['sku']], $productData);
        }
    }
    
    private function addMoreUsers()
    {
        echo "   👥 إضافة مستخدمين جدد...\n";
        
        $newUsers = [
            [
                "name" => "سارة أحمد",
                "email" => "sara@example.com",
                "password" => Hash::make("password"),
                "role" => "customer",
                "phone" => "+201234567893",
                "address" => "المنصورة، الدقهلية"
            ],
            [
                "name" => "خالد حسن",
                "email" => "khaled@example.com",
                "password" => Hash::make("password"),
                "role" => "customer",
                "phone" => "+201234567894",
                "address" => "طنطا، الغربية"
            ],
            [
                "name" => "مريم محمود",
                "email" => "mariam@example.com",
                "password" => Hash::make("password"),
                "role" => "customer",
                "phone" => "+201234567895",
                "address" => "أسوان، مصر"
            ],
            [
                "name" => "تامر علي",
                "email" => "tamer@example.com",
                "password" => Hash::make("password"),
                "role" => "customer",
                "phone" => "+201234567896",
                "address" => "الأقصر، مصر"
            ],
            [
                "name" => "نورا سالم",
                "email" => "nora@example.com",
                "password" => Hash::make("password"),
                "role" => "customer",
                "phone" => "+201234567897",
                "address" => "دمياط، مصر"
            ]
        ];
        
        foreach ($newUsers as $userData) {
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }
    }
    
    private function addProductReviews()
    {
        echo "   ⭐ إضافة مراجعات المنتجات...\n";
        
        $products = Product::all();
        $users = User::where("role", "customer")->get();
        
        if ($products->count() == 0 || $users->count() == 0) return;
        
        $reviewTexts = [
            "منتج ممتاز وجودة عالية، أنصح به بشدة",
            "سعر مناسب وأداء جيد للغاية", 
            "تم التسليم في الوقت المحدد والمنتج كما هو موصوف",
            "جودة ممتازة ومطابق للمواصفات المذكورة",
            "منتج رائع ويستحق الشراء",
            "خدمة عملاء ممتازة وشحن سريع",
            "المنتج يفوق التوقعات، راضي جداً عن الشراء"
        ];
        
        foreach ($products->take(5) as $product) {
            foreach ($users->random(3) as $user) {
                ProductReview::firstOrCreate([
                    'user_id' => $user->id,
                    'product_id' => $product->id
                ], [
                    'rating' => rand(4, 5),
                    'review' => $reviewTexts[array_rand($reviewTexts)],
                    'status' => 'approved',
                    'verified_purchase' => true,
                    'helpful_count' => rand(0, 20),
                    'created_at' => now()->subDays(rand(1, 30))
                ]);
            }
        }
    }
    
    private function addNotifications()
    {
        echo "   🔔 إضافة إشعارات متنوعة...\n";
        
        $users = User::where("role", "customer")->take(3)->get();
        
        $notifications = [
            [
                "type" => "offer",
                "message" => "عرض خاص! خصم 15% على جميع الأدوات الكهربائية"
            ],
            [
                "type" => "stock", 
                "message" => "تم إعادة تعبئة المخزون - المنتجات المطلوبة متوفرة الآن"
            ],
            [
                "type" => "order",
                "message" => "شكراً لك! تم تأكيد طلبك وجاري التحضير للشحن"
            ]
        ];
        
        foreach ($users as $user) {
            foreach ($notifications as $notificationData) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => $notificationData['type'],
                    'message' => $notificationData['message'],
                    'read_at' => rand(0, 1) ? now()->subHours(rand(1, 24)) : null
                ]);
            }
        }
    }
}