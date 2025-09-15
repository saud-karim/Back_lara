<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 فحص أعمدة جدول notifications:\n";
echo "================================\n";

$columns = DB::select('DESCRIBE notifications');
foreach ($columns as $col) {
    echo "  ✅ {$col->Field} - {$col->Type}\n";
} 