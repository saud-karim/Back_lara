<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            BrandSeeder::class, 
            EcommerceSeeder::class,       // البيانات الأساسية
            CleanSeeder::class,           // بيانات إضافية آمنة
            ExpandedDataSeeder::class,    // بيانات موسعة وغنية
        ]);

        $this->command->info("🎉 جميع السيدرز تمت بنجاح!");
        $this->command->info("📊 البيانات الآن أكثر ثراءً وتنوعاً");
        $this->command->info("🔐 حسابات الاختبار:");
        $this->command->info("   👑 Admin: admin@construction.com | password");
        $this->command->info("   👤 Customer: customer@construction.com | password");
        $this->command->info("   👤 Ahmed: ahmed@example.com | password");
        $this->command->info("   👤 Sara: sara@example.com | password");
        $this->command->info("📦 قاعدة البيانات تحتوي على بيانات شاملة ومتنوعة!");
    }
}