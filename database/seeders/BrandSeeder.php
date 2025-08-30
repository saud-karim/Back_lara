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
                'logo' => '/images/brands/dewalt.png',
                'website' => 'https://www.dewalt.com',
                'status' => 'active',
                'featured' => true,
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'مكيتا',
                'name_en' => 'Makita',
                'description_ar' => 'أدوات كهربائية يابانية عالية الجودة',
                'description_en' => 'High-quality Japanese power tools',
                'logo' => '/images/brands/makita.png',
                'website' => 'https://www.makita.com',
                'status' => 'active',
                'featured' => true,
                'sort_order' => 2,
            ],
            [
                'name_ar' => 'بوش',
                'name_en' => 'Bosch',
                'description_ar' => 'تكنولوجيا ألمانية متقدمة لأدوات البناء',
                'description_en' => 'Advanced German technology for construction tools',
                'logo' => '/images/brands/bosch.png',
                'website' => 'https://www.bosch.com',
                'status' => 'active',
                'featured' => true,
                'sort_order' => 3,
            ],
            [
                'name_ar' => 'بلاك آند ديكر',
                'name_en' => 'Black & Decker',
                'description_ar' => 'أدوات منزلية ومهنية موثوقة',
                'description_en' => 'Reliable home and professional tools',
                'logo' => '/images/brands/black-decker.png',
                'website' => 'https://www.blackanddecker.com',
                'status' => 'active',
                'featured' => false,
                'sort_order' => 4,
            ],
            [
                'name_ar' => 'ميلووكي',
                'name_en' => 'Milwaukee',
                'description_ar' => 'أدوات احترافية للصناعات الثقيلة',
                'description_en' => 'Professional tools for heavy industries',
                'logo' => '/images/brands/milwaukee.png',
                'website' => 'https://www.milwaukeetool.com',
                'status' => 'active',
                'featured' => true,
                'sort_order' => 5,
            ],
            [
                'name_ar' => 'هيلتي',
                'name_en' => 'Hilti',
                'description_ar' => 'حلول متقدمة للبناء والتشييد',
                'description_en' => 'Advanced solutions for construction',
                'logo' => '/images/brands/hilti.png',
                'website' => 'https://www.hilti.com',
                'status' => 'active',
                'featured' => true,
                'sort_order' => 6,
            ],
            [
                'name_ar' => 'ستانلي',
                'name_en' => 'Stanley',
                'description_ar' => 'أدوات يدوية وقياس عالية الدقة',
                'description_en' => 'Precision hand tools and measuring equipment',
                'logo' => '/images/brands/stanley.png',
                'website' => 'https://www.stanley.com',
                'status' => 'active',
                'featured' => false,
                'sort_order' => 7,
            ],
            [
                'name_ar' => 'ريوبي',
                'name_en' => 'Ryobi',
                'description_ar' => 'أدوات كهربائية بأسعار معقولة',
                'description_en' => 'Affordable power tools',
                'logo' => '/images/brands/ryobi.png',
                'website' => 'https://www.ryobitools.com',
                'status' => 'active',
                'featured' => false,
                'sort_order' => 8,
            ],
            [
                'name_ar' => 'فيستول',
                'name_en' => 'Festool',
                'description_ar' => 'أدوات دقيقة للحرفيين المحترفين',
                'description_en' => 'Precision tools for professional craftsmen',
                'logo' => '/images/brands/festool.png',
                'website' => 'https://www.festool.com',
                'status' => 'active',
                'featured' => true,
                'sort_order' => 9,
            ],
            [
                'name_ar' => 'كرافتسمان',
                'name_en' => 'Craftsman',
                'description_ar' => 'أدوات أمريكية تقليدية موثوقة',
                'description_en' => 'Reliable traditional American tools',
                'logo' => '/images/brands/craftsman.png',
                'website' => 'https://www.craftsman.com',
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
