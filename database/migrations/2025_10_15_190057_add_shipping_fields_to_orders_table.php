<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add tracking_number column
            if (!Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number', 255)->nullable()->after('order_number');
            }
            
            // Add shipping_company column
            if (!Schema::hasColumn('orders', 'shipping_company')) {
                $table->string('shipping_company', 50)->nullable()->after('tracking_number');
            }
            
            // Add shipping_status column
            if (!Schema::hasColumn('orders', 'shipping_status')) {
                $table->string('shipping_status', 50)->default('not_sent')->after('shipping_company');
            }
            
            // Add shipped_at timestamp
            if (!Schema::hasColumn('orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('shipping_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'tracking_number')) {
                $table->dropColumn('tracking_number');
            }
            
            if (Schema::hasColumn('orders', 'shipping_company')) {
                $table->dropColumn('shipping_company');
            }
            
            if (Schema::hasColumn('orders', 'shipping_status')) {
                $table->dropColumn('shipping_status');
            }
            
            if (Schema::hasColumn('orders', 'shipped_at')) {
                $table->dropColumn('shipped_at');
            }
        });
    }
};
