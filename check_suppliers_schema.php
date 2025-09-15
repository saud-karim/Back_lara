<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 فحص أعمدة جدول suppliers:\n";
echo "============================\n";

$columns = DB::select('DESCRIBE suppliers');
foreach ($columns as $col) {
    echo "  ✅ {$col->Field} - {$col->Type}\n";
}

echo "\n🔍 فحص أعمدة جدول products:\n";  
echo "=============================\n";

$columns = DB::select('DESCRIBE products');
foreach ($columns as $col) {
    echo "  ✅ {$col->Field} - {$col->Type}\n";
} 