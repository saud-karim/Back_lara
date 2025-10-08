<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Drop indexes that use user_id
            $table->dropUnique('unique_user_product');
            $table->dropIndex('cart_items_user_id_index');
            
            // Now drop user_id column
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Restore user_id column
            $table->foreignId('user_id')->after('cart_id')->constrained('users')->onDelete('cascade');
            
            // Restore indexes
            $table->index('user_id');
            $table->unique(['user_id', 'product_id'], 'unique_user_product');
        });
    }
};
