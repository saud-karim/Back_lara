<?php

namespace Database\Seeders;

use App\Models\Certification;
use Illuminate\Database\Seeder;

class CertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Certification::create([
            'name_ar' => 'شهادة الأيزو 9001',
            'name_en' => 'ISO 9001 Certificate',
            'description_ar' => 'شهادة إدارة الجودة الدولية',
            'description_en' => 'International Quality Management Certificate',
            'issuer_ar' => 'منظمة المعايير الدولية',
            'issuer_en' => 'International Standards Organization',
            'issue_date' => '2020-01-15',
            'expiry_date' => '2023-01-15',
            'image' => '/storage/certifications/iso9001.jpg',
            'order' => 1,
            'is_active' => true,
        ]);

        Certification::create([
            'name_ar' => 'شهادة السلامة المهنية',
            'name_en' => 'Occupational Safety Certificate',
            'description_ar' => 'شهادة معتمدة للسلامة المهنية',
            'description_en' => 'Certified occupational safety certificate',
            'issuer_ar' => 'وزارة القوى العاملة',
            'issuer_en' => 'Ministry of Manpower',
            'issue_date' => '2022-03-20',
            'expiry_date' => '2025-03-20',
            'image' => '/storage/certifications/safety.jpg',
            'order' => 2,
            'is_active' => true,
        ]);

        Certification::create([
            'name_ar' => 'شهادة الجودة البيئية',
            'name_en' => 'Environmental Quality Certificate',
            'description_ar' => 'شهادة معتمدة للجودة البيئية والاستدامة',
            'description_en' => 'Certified environmental quality and sustainability certificate',
            'issuer_ar' => 'وزارة البيئة',
            'issuer_en' => 'Ministry of Environment',
            'issue_date' => '2021-06-10',
            'expiry_date' => '2024-06-10',
            'image' => '/storage/certifications/environmental.jpg',
            'order' => 3,
            'is_active' => true,
        ]);
    }
} 