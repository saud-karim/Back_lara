<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // إنشاء فئات أولاً
        $categories = [
            [
                'id' => 'power-tools',
                'name_ar' => 'الأدوات الكهربائية',
                'name_en' => 'Power Tools',
                'description_ar' => 'أدوات كهربائية احترافية للبناء والإنشاء',
                'description_en' => 'Professional power tools for construction',
                'icon' => '🔌',
                'status' => 'active',
            ],
            [
                'id' => 'hand-tools',
                'name_ar' => 'الأدوات اليدوية',
                'name_en' => 'Hand Tools',
                'description_ar' => 'أدوات يدوية عالية الجودة',
                'description_en' => 'High quality hand tools',
                'icon' => '🔨',
                'status' => 'active',
            ],
            [
                'id' => 'safety-equipment',
                'name_ar' => 'معدات الأمان',
                'name_en' => 'Safety Equipment',
                'description_ar' => 'معدات السلامة والوقاية الشخصية',
                'description_en' => 'Personal protective equipment',
                'icon' => '🦺',
                'status' => 'active',
            ]
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['id' => $category['id']], $category);
        }

        // إنشاء منتجات تجريبية
        $products = [
            [
                'name_ar' => 'مثقاب ديوالت 20 فولت',
                'name_en' => 'DeWalt 20V Cordless Drill',
                'description_ar' => 'مثقاب احترافي لاسلكي مع بطارية ليثيوم أيون قوية. مثالي لجميع أعمال الحفر والربط.',
                'description_en' => 'Professional cordless drill with powerful lithium-ion battery. Perfect for all drilling and fastening applications.',
                'category_id' => 'power-tools',
                'price' => 1299.00,
                'original_price' => 1499.00,
                'stock_quantity' => 25,
                'sku' => 'DWT-CD20V-001',
                'rating' => 4.8,
                'reviews_count' => 156,
                'badge_ar' => 'الأكثر مبيعاً',
                'badge_en' => 'Best Seller',
                'badge_color' => '#ff6b35',
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name_ar' => 'منشار دائري بوش 190 مم',
                'name_en' => 'Bosch 190mm Circular Saw',
                'description_ar' => 'منشار دائري احترافي بقوة 1400 واط. شفرة 190 مم للقطع الدقيق والسريع.',
                'description_en' => 'Professional circular saw with 1400W power. 190mm blade for precise and fast cutting.',
                'category_id' => 'power-tools',
                'price' => 899.00,
                'original_price' => 999.00,
                'stock_quantity' => 18,
                'sku' => 'BSH-CS190-002',
                'rating' => 4.6,
                'reviews_count' => 89,
                'badge_ar' => 'جديد',
                'badge_en' => 'New',
                'badge_color' => '#28a745',
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name_ar' => 'مطرقة احترافية 500 جرام',
                'name_en' => 'Professional Hammer 500g',
                'description_ar' => 'مطرقة احترافية بمقبض مطاطي مقاوم للانزلاق. وزن 500 جرام مثالي للاستخدام العام.',
                'description_en' => 'Professional hammer with anti-slip rubber handle. 500g weight perfect for general use.',
                'category_id' => 'hand-tools',
                'price' => 149.00,
                'original_price' => null,
                'stock_quantity' => 50,
                'sku' => 'HMR-500G-003',
                'rating' => 4.4,
                'reviews_count' => 34,
                'badge_ar' => null,
                'badge_en' => null,
                'badge_color' => null,
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name_ar' => 'مجموعة مفاتيح ربط 12 قطعة',
                'name_en' => 'Wrench Set 12 Pieces',
                'description_ar' => 'مجموعة مفاتيح ربط من الكروم الفاناديوم عالي الجودة. 12 مقاس مختلف من 8 إلى 19 مم.',
                'description_en' => 'High-quality chrome vanadium wrench set. 12 different sizes from 8 to 19mm.',
                'category_id' => 'hand-tools',
                'price' => 299.00,
                'original_price' => 349.00,
                'stock_quantity' => 30,
                'sku' => 'WRS-12PC-004',
                'rating' => 4.7,
                'reviews_count' => 67,
                'badge_ar' => 'عرض خاص',
                'badge_en' => 'Special Offer',
                'badge_color' => '#dc3545',
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name_ar' => 'خوذة أمان بيضاء',
                'name_en' => 'White Safety Helmet',
                'description_ar' => 'خوذة أمان معتمدة دولياً لحماية الرأس في مواقع البناء. مقاومة للصدمات والكسر.',
                'description_en' => 'Internationally certified safety helmet for head protection at construction sites. Impact and break resistant.',
                'category_id' => 'safety-equipment',
                'price' => 89.00,
                'original_price' => null,
                'stock_quantity' => 100,
                'sku' => 'SFH-WHT-005',
                'rating' => 4.3,
                'reviews_count' => 112,
                'badge_ar' => null,
                'badge_en' => null,
                'badge_color' => null,
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name_ar' => 'نظارات أمان شفافة',
                'name_en' => 'Clear Safety Glasses',
                'description_ar' => 'نظارات أمان شفافة مقاومة للخدش والضربات. حماية عين فائقة للعمال.',
                'description_en' => 'Clear safety glasses resistant to scratches and impacts. Superior eye protection for workers.',
                'category_id' => 'safety-equipment',
                'price' => 45.00,
                'original_price' => null,
                'stock_quantity' => 200,
                'sku' => 'SFG-CLR-006',
                'rating' => 4.1,
                'reviews_count' => 78,
                'badge_ar' => null,
                'badge_en' => null,
                'badge_color' => null,
                'status' => 'active',
                'featured' => false,
            ],
            [
                'name_ar' => 'مثقاب مكيتا 18 فولت',
                'name_en' => 'Makita 18V Cordless Drill',
                'description_ar' => 'مثقاب لاسلكي من مكيتا بقوة 18 فولت. بطارية سريعة الشحن ومحرك فرشاة.',
                'description_en' => 'Makita cordless drill with 18V power. Fast charging battery and brushed motor.',
                'category_id' => 'power-tools',
                'price' => 1099.00,
                'original_price' => 1199.00,
                'stock_quantity' => 15,
                'sku' => 'MKT-CD18V-007',
                'rating' => 4.5,
                'reviews_count' => 203,
                'badge_ar' => 'مُوصى به',
                'badge_en' => 'Recommended',
                'badge_color' => '#6f42c1',
                'status' => 'active',
                'featured' => true,
            ],
            [
                'name_ar' => 'صندوق عدة معدني كبير',
                'name_en' => 'Large Metal Tool Box',
                'description_ar' => 'صندوق عدة معدني قوي مع أقفال آمنة. مقسم داخلياً لتنظيم الأدوات بكفاءة.',
                'description_en' => 'Strong metal tool box with secure locks. Internally divided for efficient tool organization.',
                'category_id' => 'hand-tools',
                'price' => 399.00,
                'original_price' => null,
                'stock_quantity' => 12,
                'sku' => 'TBX-MTL-008',
                'rating' => 4.2,
                'reviews_count' => 45,
                'badge_ar' => null,
                'badge_en' => null,
                'badge_color' => null,
                'status' => 'active',
                'featured' => false,
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('تم إنشاء ' . count($products) . ' منتجات بنجاح!');
    }
} 