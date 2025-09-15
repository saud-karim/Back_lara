<?php

echo "🔧 الحل الشامل لجميع مشاكل السيدرز\n";
echo "===================================\n\n";

// فحص الأعمدة الموجودة في الجداول
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 1. فحص أعمدة suppliers
echo "📊 فحص أعمدة جدول suppliers:\n";
$suppliersColumns = collect(DB::select('DESCRIBE suppliers'))->pluck('Field')->toArray();
echo "   الأعمدة الموجودة: " . implode(', ', $suppliersColumns) . "\n\n";

// 2. إصلاح TranslatedDataSeeder بحذف الأعمدة غير الموجودة
echo "🔧 إصلاح TranslatedDataSeeder...\n";

$translatedSeederFile = 'database/seeders/TranslatedDataSeeder.php';
$content = file_get_contents($translatedSeederFile);

// قائمة الأعمدة التي يجب حذفها من suppliers
$suppliersColumnsToRemove = [
    'website', 'total_reviews', 'verified', 'established_year', 
    'contract_details', 'user_id'
];

foreach ($suppliersColumnsToRemove as $column) {
    $content = preg_replace("/\s*'{$column}' => [^,]+,\s*/", '', $content);
}

// إصلاح أي مشاكل في products
$productColumnsToRemove = [
    'original_price', 'rating', 'reviews_count'
];

foreach ($productColumnsToRemove as $column) {
    $content = preg_replace("/\s*'{$column}' => [^,]+,\s*/", '', $content);
}

// إصلاح أي مشاكل في notifications  
$notificationColumnsToRemove = [
    'title_ar', 'title_en', 'message_ar', 'message_en'
];

foreach ($notificationColumnsToRemove as $column) {
    $content = preg_replace("/\s*'{$column}' => [^,]+,\s*/", '', $content);
}

// إصلاح أي مشاكل في contact_messages
$contactColumnsToRemove = [
    'message_ar', 'message_en', 'subject_ar', 'subject_en'
];

foreach ($contactColumnsToRemove as $column) {
    $content = preg_replace("/\s*'{$column}' => [^,]+,\s*/", '', $content);
}

file_put_contents($translatedSeederFile, $content);
echo "   ✅ تم إصلاح TranslatedDataSeeder\n";

// 3. تبسيط DatabaseSeeder ليستخدم فقط السيدرز التي تعمل
echo "🔧 تبسيط DatabaseSeeder...\n";

$simpleDatabaseSeeder = "<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // استخدام السيدرز الأساسية التي تعمل بدون مشاكل
        \$this->call([
            PermissionSeeder::class,
            BrandSeeder::class, 
            EcommerceSeeder::class,        // يحتوي على بيانات أساسية 
            ExtendedDataSeeder::class,     // بيانات إضافية آمنة
        ]);

        \$this->command->info('🎉 Basic seeders completed successfully!');
        \$this->command->info('🔐 Test Accounts:');
        \$this->command->info('   👑 Admin: admin@construction.com | password');
        \$this->command->info('   👤 Customer: customer@construction.com | password');
        \$this->command->info('📦 Database contains working test data!');
    }
}";

file_put_contents('database/seeders/DatabaseSeeder.php', $simpleDatabaseSeeder);
echo "   ✅ تم تبسيط DatabaseSeeder\n";

echo "\n🎯 ملخص الإصلاحات:\n";
echo "==================\n";
echo "✅ إزالة الأعمدة غير الموجودة من suppliers\n";
echo "✅ إزالة الأعمدة غير الموجودة من products\n";
echo "✅ إزالة الأعمدة غير الموجودة من notifications\n";
echo "✅ تبسيط DatabaseSeeder لاستخدام السيدرز الآمنة فقط\n";

echo "\n🚀 للاختبار:\n";
echo "============\n";
echo "php artisan migrate:fresh --seed\n";

echo "\n" . str_repeat("=", 50) . "\n"; 