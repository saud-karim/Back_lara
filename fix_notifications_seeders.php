<?php

echo "🔧 إصلاح مشكلة message_en في السيدرز\n";
echo "===================================\n\n";

// 1. إصلاح EcommerceSeeder
echo "1️⃣ إصلاح EcommerceSeeder...\n";

$ecommerceSeederFile = 'database/seeders/EcommerceSeeder.php';
$content = file_get_contents($ecommerceSeederFile);

// استبدال message_en بـ message
$content = str_replace("'message_en'", "'message'", $content);

file_put_contents($ecommerceSeederFile, $content);
echo "   ✅ تم إصلاح EcommerceSeeder\n";

// 2. إصلاح TranslatedDataSeeder
echo "2️⃣ إصلاح TranslatedDataSeeder...\n";

$translatedSeederFile = 'database/seeders/TranslatedDataSeeder.php';
$content = file_get_contents($translatedSeederFile);

// استبدال message_en بـ message
$content = str_replace("'message_en'", "'message'", $content);

file_put_contents($translatedSeederFile, $content);
echo "   ✅ تم إصلاح TranslatedDataSeeder\n";

echo "\n✅ تم إصلاح جميع مشاكل notifications\n";
echo "الآن يمكن تشغيل: php artisan migrate:fresh --seed\n";

echo "\n" . str_repeat("=", 50) . "\n"; 