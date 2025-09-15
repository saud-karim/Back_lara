<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name_ar' => 'ديوالت',
                'name_en' => 'DeWalt',
                'description_ar' => 'علامة تجارية رائدة في أدوات البناء الكهربائية',
                'description_en' => 'Leading brand in power construction tools',
                'status' => 'active',
                'featured' => true,
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'مكيتا',
                'name_en' => 'Makita',
                'description_ar' => 'أدوات كهربائية يابانية عالية الجودة',
                'description_en' => 'High-quality Japanese power tools',
                'status' => 'active',
                'featured' => true,
                'sort_order' => 2,
            ],
            [
                'name_ar' => 'بوش',
                'name_en' => 'Bosch',
                'description_ar' => 'تكنولوجيا ألمانية متقدمة لأدوات البناء',
                'description_en' => 'Advanced German technology for construction tools',
                'status' => 'active',
                'featured' => true,
                'sort_order' => 3,
            ],
            [
                'name_ar' => 'بلاك آند ديكر',
                'name_en' => 'Black & Decker',
                'description_ar' => 'أدوات منزلية ومهنية موثوقة',
                'description_en' => 'Reliable home and professional tools',
                'status' => 'active',
                'featured' => false,
                'sort_order' => 4,
            ],
            [
                'name_ar' => 'ميلووكي',
                'name_en' => 'Milwaukee',
                'description_ar' => 'أدوات احترافية للصناعات الثقيلة',
                'description_en' => 'Professional tools for heavy industries',
                'status' => 'active',
                'featured' => true,
                'sort_order' => 5,
            ],
            [
                'name_ar' => 'هيلتي',
                'name_en' => 'Hilti',
                'description_ar' => 'حلول متقدمة للبناء والتشييد',
                'description_en' => 'Advanced solutions for construction',
                'status' => 'active',
                'featured' => true,
                'sort_order' => 6,
            ],
            [
                'name_ar' => 'ستانلي',
                'name_en' => 'Stanley',
                'description_ar' => 'أدوات يدوية وقياس عالية الدقة',
                'description_en' => 'Precision hand tools and measuring equipment',
                'status' => 'active',
                'featured' => false,
                'sort_order' => 7,
            ],
            [
                'name_ar' => 'ريوبي',
                'name_en' => 'Ryobi',
                'description_ar' => 'أدوات كهربائية بأسعار معقولة',
                'description_en' => 'Affordable power tools',
                'status' => 'active',
                'featured' => false,
                'sort_order' => 8,
            ],
            [
                'name_ar' => 'فيستول',
                'name_en' => 'Festool',
                'description_ar' => 'أدوات دقيقة للحرفيين المحترفين',
                'description_en' => 'Precision tools for professional craftsmen',
                'status' => 'active',
                'featured' => true,
                'sort_order' => 9,
            ],
            [
                'name_ar' => 'كرافتسمان',
                'name_en' => 'Craftsman',
                'description_ar' => 'أدوات أمريكية تقليدية موثوقة',
                'description_en' => 'Reliable traditional American tools',
                'status' => 'active',
                'featured' => false,
                'sort_order' => 10,
            ],
        ];

        foreach ($brands as $brand) {
            \App\Models\Brand::create($brand);
        }
    }
}
