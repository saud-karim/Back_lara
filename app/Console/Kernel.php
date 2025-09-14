<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // تنظيف السلات من المنتجات المحذوفة أو غير النشطة يومياً في الساعة 2 صباحاً
        $schedule->call(function () {
            $deletedCount = DB::delete('
                DELETE cart_items 
                FROM cart_items 
                LEFT JOIN products ON cart_items.product_id = products.id 
                WHERE products.id IS NULL 
                   OR products.status != "active"
                   OR products.deleted_at IS NOT NULL
            ');
            
            if ($deletedCount > 0) {
                \Log::info("تم تنظيف {$deletedCount} عنصر من السلات المختلفة.");
            }
        })->dailyAt('02:00')->name('clean-invalid-cart-items');

        // تنظيف السلات القديمة (أكثر من 30 يوم) كل أسبوع
        $schedule->call(function () {
            $deletedCount = DB::table('cart_items')
                ->where('created_at', '<', now()->subDays(30))
                ->delete();
                
            if ($deletedCount > 0) {
                \Log::info("تم تنظيف {$deletedCount} عنصر قديم من السلات.");
            }
        })->weekly()->name('clean-old-cart-items');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
