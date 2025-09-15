<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Brand;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\Address;
use App\Models\ContactMessage;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class TranslatedDataSeeder extends Seeder
{
    public function run()
    {
        echo "=== إضافة بيانات شاملة مترجمة ===" . PHP_EOL;
        
        // 1. تحديث الموردين
        $this->updateSuppliers();
        
        // 2. إضافة فئات مترجمة جديدة  
        $this->addCategories();
        
        // 3. إضافة منتجات مترجمة
        $this->addProducts();
        
        // 4. إضافة مستخدمين
        $this->addUsers();
        
        // 5. إضافة عناوين للمستخدمين
        $this->addAddresses();
        
        // 6. إضافة طلبات
        $this->addOrders();
        
        // 7. إضافة مراجعات
        $this->addReviews();
        
        // 8. إضافة رسائل اتصال
        $this->addContactMessages();
        
        // 9. إضافة إشعارات
        $this->addNotifications();
        
        // 10. تحديث الكوبونات
        $this->updateCoupons();
        
        echo PHP_EOL . "🎉 تم إكمال إضافة جميع البيانات المترجمة!" . PHP_EOL;
    }
    
    private function updateSuppliers()
    {
        echo "1. تحديث الموردين..." . PHP_EOL;
        
        // تحديث المورد الأول
        Supplier::where('id', 1)->update([
            'name_ar' => 'شركة الأدوات المتقدمة',
            'name_en' => 'Advanced Tools Company',
            'description_ar' => 'مورد رائد في الأدوات الكهربائية والمعدات الصناعية في منطقة الشرق الأوسط',
            'description_en' => 'Leading supplier of power tools and industrial equipment in the Middle East region'
        ]);
        
        // إضافة موردين جدد
        $suppliers = [
            [
                'name_ar' => 'مؤسسة البناء الحديث',
                'name_en' => 'Modern Construction Foundation',
                'description_ar' => 'متخصصون في توريد مواد البناء والأسمنت والحديد عالي الجودة لمشاريع البنية التحتية',
                'description_en' => 'Specialists in supplying high-quality building materials, cement, and steel for infrastructure projects',
                'email' => 'info@modern-construction.com',
                'phone' => '+201234567891',
                'address' => 'المنطقة الصناعية، الإسكندرية، مصر','certifications' => json_encode(['ISO 9001:2008', 'Quality Assurance Certificate']),'payment_terms' => '15_days']),
                'status' => 'active'
            ],
            [
                'name_ar' => 'شركة التجهيزات الهندسية',
                'name_en' => 'Engineering Equipment Company', 
                'description_ar' => 'نوفر أحدث المعدات الهندسية والآلات المتخصصة لمشاريع البنية التحتية والصناعة',
                'description_en' => 'We provide the latest engineering equipment and specialized machinery for infrastructure and industrial projects',
                'email' => 'contact@engequipment.com',
                'phone' => '+201234567892',
                'address' => 'مدينة نصر، القاهرة، مصر','certifications' => json_encode(['CE Marking', 'ISO 14001:2015']),'warranty_provider' => true]),
                'status' => 'active'
            ]
        ];
        
        foreach($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }
        
        echo "   ✅ تم تحديث وإضافة الموردين" . PHP_EOL;
    }
    
    private function addCategories()
    {
        echo "2. إضافة فئات مترجمة..." . PHP_EOL;
        
        $categories = [
            [
                'id' => 'safety-equipment',
                'name_ar' => 'معدات السلامة',
                'name_en' => 'Safety Equipment',
                'description_ar' => 'معدات وأدوات السلامة المهنية لضمان حماية العمال في مواقع البناء والمصانع',
                'description_en' => 'Professional safety equipment and tools to ensure worker protection at construction sites and factories',
                'image' => '/images/categories/safety-equipment.jpg',
                'parent_id' => null,
                'sort_order' => 20,
                'status' => 'active'
            ],
            [
                'id' => 'measuring-tools',
                'name_ar' => 'أدوات القياس',
                'name_en' => 'Measuring Tools',
                'description_ar' => 'أدوات دقيقة لقياس الأطوال والزوايا والمستويات في أعمال البناء والهندسة',
                'description_en' => 'Precise tools for measuring lengths, angles, and levels in construction and engineering work',
                'image' => '/images/categories/measuring-tools.jpg',
                'parent_id' => null,
                'sort_order' => 21,
                'status' => 'active'
            ],
            [
                'id' => 'electrical-equipment',
                'name_ar' => 'المعدات الكهربائية',
                'name_en' => 'Electrical Equipment',
                'description_ar' => 'أدوات ومعدات كهربائية متخصصة لأعمال التمديدات والصيانة الكهربائية',
                'description_en' => 'Specialized electrical tools and equipment for wiring and electrical maintenance work',
                'image' => '/images/categories/electrical-equipment.jpg',
                'parent_id' => null,
                'sort_order' => 22,
                'status' => 'active'
            ]
        ];
        
        foreach($categories as $categoryData) {
            Category::updateOrCreate(['id' => $categoryData['id']], $categoryData);
        }
        
        echo "   ✅ تم إضافة " . count($categories) . " فئة جديدة" . PHP_EOL;
    }
    
    private function addProducts()
    {
        echo "3. إضافة منتجات مترجمة..." . PHP_EOL;
        
        $brandBosch = Brand::where('name_en', 'Bosch')->first();
        $brandDeWalt = Brand::where('name_en', 'DeWalt')->first();
        $brandMakita = Brand::where('name_en', 'Makita')->first();
        
        $products = [
            [
                'name_ar' => 'مثقاب كهربائي بوش GSB 120-LI',
                'name_en' => 'Bosch GSB 120-LI Cordless Drill',
                'description_ar' => 'مثقاب كهربائي لاسلكي احترافي مع بطارية ليثيوم أيون 12 فولت. مثالي لجميع أعمال الثقب والبراغي في المواد المختلفة مثل الخشب والمعدن والبلاستيك.',
                'description_en' => 'Professional cordless drill with 12V lithium-ion battery. Perfect for all drilling and screwing tasks in various materials like wood, metal, and plastic.',
                'category_id' => 'power-tools',
                'price' => 299.99,'stock' => 50,
                'sku' => 'BSH-GSB120-001','status' => 'active',
                'featured' => true,
                'supplier_id' => 1,
                'brand_id' => $brandBosch?->id,
                'images' => json_encode([
                    '/images/products/bosch-drill-1.jpg',
                    '/images/products/bosch-drill-2.jpg',
                    '/images/products/bosch-drill-3.jpg'
                ])
            ],
            [
                'name_ar' => 'منشار دائري ديوالت DWE575',
                'name_en' => 'DeWalt DWE575 Circular Saw',
                'description_ar' => 'منشار دائري كهربائي بمحرك 1600 واط وعمق قطع حتى 67 مم. مزود بحماية من الغبار وقاعدة من الألمنيوم للاستقرار.',
                'description_en' => 'Electric circular saw with 1600W motor and cutting depth up to 67mm. Features dust protection and aluminum base for stability.',
                'category_id' => 'power-tools',
                'price' => 189.99,'stock' => 35,
                'sku' => 'DW-DWE575-002','status' => 'active',
                'featured' => true,
                'supplier_id' => 1,
                'brand_id' => $brandDeWalt?->id,
                'images' => json_encode(['/images/products/dewalt-saw-1.jpg'])
            ],
            [
                'name_ar' => 'خوذة أمان صناعية مع واقي وجه',
                'name_en' => 'Industrial Safety Helmet with Face Shield',
                'description_ar' => 'خوذة أمان عالية الجودة مصنوعة من البولي إيثيلين المقاوم للصدمات مع واقي وجه شفاف قابل للطي. تلبي معايير السلامة الدولية.',
                'description_en' => 'High-quality safety helmet made of impact-resistant polyethylene with foldable transparent face shield. Meets international safety standards.',
                'category_id' => 'safety-equipment',
                'price' => 45.50,'stock' => 120,
                'sku' => 'SAF-HLM-003','status' => 'active',
                'featured' => true,
                'supplier_id' => 3,
                'brand_id' => null,
                'images' => json_encode(['/images/products/safety-helmet-1.jpg'])
            ],
            [
                'name_ar' => 'مقياس ليزر رقمي بوش GLM 50',
                'name_en' => 'Bosch GLM 50 Digital Laser Measure',
                'description_ar' => 'مقياس مسافة بالليزر عالي الدقة مع مدى 50 متر ودقة ±1.5 مم. مزود بشاشة مضيئة وذاكرة للقراءات.',
                'description_en' => 'High-precision laser distance meter with 50m range and ±1.5mm accuracy. Features illuminated display and memory for readings.',
                'category_id' => 'measuring-tools',
                'price' => 129.99,'stock' => 40,
                'sku' => 'BSH-GLM50-004','status' => 'active',
                'featured' => true,
                'supplier_id' => 1,
                'brand_id' => $brandBosch?->id,
                'images' => json_encode(['/images/products/laser-measure-1.jpg'])
            ],
            [
                'name_ar' => 'أسمنت بورتلاند أبيض 50 كيلو',
                'name_en' => 'White Portland Cement 50kg',
                'description_ar' => 'أسمنت بورتلاند أبيض عالي الجودة مناسب لجميع أعمال البناء والخرسانة والديكور. معبأ في أكياس محكمة الإغلاق.',
                'description_en' => 'High-quality white Portland cement suitable for all construction, concrete, and decorative work. Packed in sealed bags.',
                'category_id' => 'raw-materials',
                'price' => 35.00,'stock' => 300,
                'sku' => 'CEM-WP50-005','status' => 'active',
                'featured' => false,
                'supplier_id' => 2,
                'brand_id' => null,
                'images' => json_encode(['/images/products/white-cement-1.jpg'])
            ]
        ];
        
        foreach($products as $productData) {
            $product = Product::create($productData);
            
            // إضافة خصائص المنتج
            $features = [
                [
                    'product_id' => $product->id,
                    'feature_ar' => 'جودة عالية ومتانة استثنائية',
                    'feature_en' => 'High quality and exceptional durability',
                    'sort_order' => 1
                ],
                [
                    'product_id' => $product->id,
                    'feature_ar' => 'تصميم مريح وآمن للاستخدام',
                    'feature_en' => 'Comfortable and safe design for use',
                    'sort_order' => 2
                ],
                [
                    'product_id' => $product->id,
                    'feature_ar' => 'ضمان شامل لمدة سنتين',
                    'feature_en' => 'Comprehensive 2-year warranty',
                    'sort_order' => 3
                ]
            ];
            
            DB::table('product_features')->insert($features);
            
            // إضافة مواصفات المنتج
            $specifications = [
                [
                    'product_id' => $product->id,
                    'spec_key' => 'warranty',
                    'spec_value_ar' => 'سنتان',
                    'spec_value_en' => '2 years'
                ],
                [
                    'product_id' => $product->id,
                    'spec_key' => 'origin',
                    'spec_value_ar' => 'ألمانيا',
                    'spec_value_en' => 'Germany'
                ],
                [
                    'product_id' => $product->id,
                    'spec_key' => 'certification',
                    'spec_value_ar' => 'معتمد من CE',
                    'spec_value_en' => 'CE Certified'
                ]
            ];
            
            DB::table('product_specifications')->insert($specifications);
        }
        
        echo "   ✅ تم إضافة " . count($products) . " منتج مترجم" . PHP_EOL;
    }
    
    private function addUsers()
    {
        echo "4. إضافة مستخدمين جدد..." . PHP_EOL;
        
        $users = [
            [
                'name' => 'محمد أحمد',
                'email' => 'mohamed@construction.com',
                'phone' => '+201234567890',
                'address' => 'شارع التحرير، القاهرة، مصر',
                'company' => 'شركة البناء المتقدم',
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now()
            ],
            [
                'name' => 'فاطمة علي',
                'email' => 'fatma@engineering.com',
                'phone' => '+201234567891',
                'address' => 'مدينة نصر، القاهرة، مصر',
                'company' => 'مكتب الهندسة الحديثة',
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now()
            ],
            [
                'name' => 'أحمد حسن',
                'email' => 'ahmed@contractor.com',
                'phone' => '+201234567892',
                'address' => 'الإسكندرية، مصر',
                'company' => 'مقاولات حسن للبناء',
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now()
            ]
        ];
        
        foreach($users as $userData) {
            $userData['password'] = Hash::make('password');
            User::create($userData);
        }
        
        echo "   ✅ تم إضافة " . count($users) . " مستخدم جديد" . PHP_EOL;
    }
    
    private function updateCoupons()
    {
        echo "10. تحديث الكوبونات..." . PHP_EOL;
        
        $coupons = DB::table('coupons')->get();
        foreach($coupons as $coupon) {
            $updates = [];
            
            switch($coupon->code) {
                case 'SAVE10':
                    $updates = [
                        'description_ar' => 'خصم 10% - عرض ترحيبي للعملاء الجدد مع توصيل مجاني',
                        'description_en' => '10% discount - Welcome offer for new customers with free shipping'
                    ];
                    break;
                case 'WELCOME20':
                    $updates = [
                        'description_ar' => 'خصم 20% على أول طلب للعملاء الجدد',
                        'description_en' => '20% off your first order for new customers'
                    ];
                    break;
                case 'SUMMER15':
                    $updates = [
                        'description_ar' => 'عرض الصيف - خصم 15% على جميع الأدوات الكهربائية',
                        'description_en' => 'Summer offer - 15% off all power tools'
                    ];
                    break;
                case 'BUILD50':
                    $updates = [
                        'description_ar' => 'خصم 50 جنيه على طلبات مواد البناء أكثر من 500 جنيه',
                        'description_en' => '50 EGP discount on building materials orders over 500 EGP'
                    ];
                    break;
                case 'TOOLS25':
                    $updates = [
                        'description_ar' => 'خصم 25% على جميع الأدوات اليدوية والكهربائية',
                        'description_en' => '25% off all hand tools and power tools'
                    ];
                    break;
            }
            
            if(!empty($updates)) {
                DB::table('coupons')->where('id', $coupon->id)->update($updates);
            }
        }
        
        echo "   ✅ تم تحديث ترجمات الكوبونات" . PHP_EOL;
    }
    
    private function addAddresses()
    {
        echo "5. إضافة عناوين للمستخدمين..." . PHP_EOL;
        
        $users = User::where('role', 'customer')->take(3)->get();
        
        foreach($users as $index => $user) {
            $addresses = [
                ['type' => 'home',
                    'name' => 'المنزل',
                    'phone' => $user->phone,
                    'street' => 'شارع ' . ($index + 1) . '، مبني رقم ' . (20 + $index),
                    'city' => 'القاهرة',
                    'state' => 'القاهرة',
                    'postal_code' => '1234' . $index,
                    'country' => 'مصر',
                    'is_default' => true
                ],
                ['type' => 'work',
                    'name' => 'العمل',
                    'phone' => $user->phone,
                    'street' => 'شارع العمل ' . ($index + 1) . '، مكتب رقم ' . (100 + $index),
                    'city' => 'الجيزة',
                    'state' => 'الجيزة',
                    'postal_code' => '5678' . $index,
                    'country' => 'مصر',
                    'is_default' => false
                ]
            ];
            
            foreach($addresses as $addressData) {
                Address::create($addressData);
            }
        }
        
        echo "   ✅ تم إضافة عناوين للمستخدمين" . PHP_EOL;
    }
    
    private function addOrders()
    {
        echo "6. إضافة طلبات..." . PHP_EOL;
        
        $users = User::where('role', 'customer')->take(3)->get();
        $products = Product::take(5)->get();
        
        foreach($users as $index => $user) {
            $order = Order::create([
                'id' => 'ORD-2024-' . str_pad($index + 10, 3, '0', STR_PAD_LEFT),'status' => ['pending', 'processing', 'delivered'][rand(0, 2)],
                'subtotal' => 500.00,
                'tax_amount' => 50.00,
                'shipping_amount' => 25.00,
                'discount_amount' => 0,
                'total_amount' => 575.00,
                'currency' => 'EGP',
                'payment_method' => 'credit_card',
                'payment_status' => 'paid',
                'shipping_address' => json_encode([
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'street' => 'شارع التحرير، مبني رقم ' . (10 + $index),
                    'city' => 'القاهرة',
                    'country' => 'مصر'
                ]),
                'notes' => 'توصيل في أقرب وقت ممكن',
                'created_at' => now()->subDays(rand(1, 30))
            ]);
            
            // إضافة عناصر الطلب
            foreach($products->take(rand(2, 4)) as $product) {
                DB::table('order_items')->insert([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'unit_price' => $product->price,
                    'total_price' => $product->price * rand(1, 3)
                ]);
            }
        }
        
        echo "   ✅ تم إضافة طلبات للمستخدمين" . PHP_EOL;
    }
    
    private function addReviews()
    {
        echo "7. إضافة مراجعات..." . PHP_EOL;
        
        $users = User::where('role', 'customer')->take(3)->get();
        $products = Product::take(5)->get();
        
        $reviews = [
            'منتج ممتاز وجودة عالية، أنصح بشرائه',
            'استخدمته في عدة مشاريع وكانت النتائج رائعة',
            'جودة ممتازة وسعر مناسب، سأشتري مرة أخرى',
            'منتج جيد جداً ولكن التوصيل كان متأخراً قليلاً',
            'راضي جداً عن الشراء، يستحق السعر'
        ];
        
        foreach($users as $user) {
            foreach($products->take(3) as $product) {
                ProductReview::create(['product_id' => $product->id,
                    'review' => $reviews[array_rand($reviews)],
                    'status' => 'approved',
                    'verified_purchase' => true,
                    'helpful_count' => rand(0, 15),
                    'created_at' => now()->subDays(rand(1, 60))
                ]);
            }
        }
        
        echo "   ✅ تم إضافة مراجعات للمنتجات" . PHP_EOL;
    }
    
    private function addContactMessages()
    {
        echo "8. إضافة رسائل اتصال..." . PHP_EOL;
        
        $messages = [
            [
                'name' => 'سارة محمد',
                'email' => 'sara@email.com',
                'phone' => '+201234567893',
                'company' => 'شركة التصميم الحديث','message' => 'I want to know more about the power tools you have available and their prices',
                'project_type' => 'commercial',
                'status' => 'new'
            ],
            [
                'name' => 'خالد أحمد',
                'email' => 'khaled@contractor.com',
                'phone' => '+201234567894',
                'company' => 'مقاولات خالد','message' => 'I have a large construction project and need a comprehensive quote for tools and equipment',
                'project_type' => 'industrial',
                'status' => 'in_progress'
            ]
        ];
        
        foreach($messages as $messageData) {
            ContactMessage::create($messageData);
        }
        
        echo "   ✅ تم إضافة رسائل الاتصال" . PHP_EOL;
    }
    
    private function addNotifications()
    {
        echo "9. إضافة إشعارات..." . PHP_EOL;
        
        $users = User::where('role', 'customer')->take(3)->get();
        
        $notifications = [
            [
                'type' => 'order_update','message' => 'Your order has been shipped and will arrive in 2-3 business days'
            ],
            [
                'type' => 'promotion','message' => '20% discount on all power tools for limited time'
            ],
            [
                'type' => 'welcome','message' => 'Welcome to our store, we hope you have a great shopping experience'
            ]
        ];
        
        foreach($users as $user) {
            foreach($notifications as $notificationData) {
                $notificationData['user_id'] = $user->id;
                $notificationData['data'] = json_encode(['order_id' => 'ORD-2024-001']);
                $notificationData['created_at'] = now()->subDays(rand(1, 10));
                
                DB::table('notifications')->insert($notificationData);
            }
        }
        
        echo "   ✅ تم إضافة إشعارات للمستخدمين" . PHP_EOL;
    }
} 