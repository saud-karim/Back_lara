<?php

echo "🔧 إصلاح السيدرز لتتوافق مع الـ schema الحالي\n";
echo "==============================================\n\n";

// 1. إصلاح TranslatedDataSeeder
echo "1️⃣ إصلاح TranslatedDataSeeder...\n";

$translatedSeederFile = 'database/seeders/TranslatedDataSeeder.php';
$content = file_get_contents($translatedSeederFile);

// إزالة الأعمدة غير الموجودة وتعديل original_price إلى sale_price
$content = preg_replace("/\s*'original_price' => [^,]+,\s*/", '', $content);
$content = preg_replace("/\s*'rating' => [^,]+,\s*/", '', $content);  
$content = preg_replace("/\s*'reviews_count' => [^,]+,\s*/", '', $content);

// تغيير أي متبقي من original_price إلى sale_price (إذا وجد)
$content = str_replace("'original_price'", "'sale_price'", $content);

file_put_contents($translatedSeederFile, $content);
echo "   ✅ تم إصلاح TranslatedDataSeeder\n";

// 2. إصلاح ProductSeeder
echo "2️⃣ إصلاح ProductSeeder...\n";

$productSeederFile = 'database/seeders/ProductSeeder.php';
if (file_exists($productSeederFile)) {
    $content = file_get_contents($productSeederFile);
    
    // إزالة الأعمدة غير الموجودة وتعديل original_price إلى sale_price
    $content = preg_replace("/\s*'original_price' => [^,]+,\s*/", '', $content);
    $content = preg_replace("/\s*'rating' => [^,]+,\s*/", '', $content);
    $content = preg_replace("/\s*'reviews_count' => [^,]+,\s*/", '', $content);
    
    // تغيير أي متبقي من original_price إلى sale_price
    $content = str_replace("'original_price'", "'sale_price'", $content);
    
    file_put_contents($productSeederFile, $content);
    echo "   ✅ تم إصلاح ProductSeeder\n";
} else {
    echo "   ⚠️  ProductSeeder غير موجود\n";
}

// 3. تحديث DatabaseSeeder لاستخدام السيدرز المفيدة
echo "3️⃣ تحديث DatabaseSeeder...\n";

$databaseSeederFile = 'database/seeders/DatabaseSeeder.php';
$newDatabaseSeederContent = "<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \$this->call([
            PermissionSeeder::class,
            BrandSeeder::class, 
            EcommerceSeeder::class,
            TranslatedDataSeeder::class,      // أضافة السيدر المفيد
            ExtendedDataSeeder::class,
            AssignRolesToUsersSeeder::class,
        ]);

        \$this->command->info('🎉 All seeders completed successfully!');
        \$this->command->info('🔐 Test Accounts Created:');
        \$this->command->info('   👑 Admin: admin@construction.com | password: password');
        \$this->command->info('   👤 Customer: customer@example.com | password: password');
        \$this->command->info('   🏭 Supplier: supplier@construction.com | password: password');
        \$this->command->info('📦 Database now contains rich multilingual test data!');
        \$this->command->info('📊 Data includes: Users, Products, Categories, Orders, Reviews');
    }
}";

file_put_contents($databaseSeederFile, $newDatabaseSeederContent);
echo "   ✅ تم تحديث DatabaseSeeder\n";

echo "\n🎯 ملخص التحديثات:\n";
echo "=================\n";
echo "✅ TranslatedDataSeeder: إزالة original_price, rating, reviews_count\n";
echo "✅ ProductSeeder: إصلاح نفس المشاكل\n";  
echo "✅ DatabaseSeeder: إضافة TranslatedDataSeeder للاستخدام\n";
echo "✅ جميع السيدرز الآن متوافقة مع schema الحالي\n";

echo "\n🚀 للتطبيق:\n";
echo "==========\n";
echo "php artisan migrate:fresh --seed\n";

echo "\n" . str_repeat("=", 50) . "\n"; 