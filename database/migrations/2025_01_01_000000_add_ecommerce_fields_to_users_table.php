<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'customer', 'supplier'])->default('customer')->after('password');
            $table->text('address')->nullable()->after('role');
            $table->string('phone')->nullable()->after('address');
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropSoftDeletes();
            $table->dropColumn(['role', 'address', 'phone']);
        });
    }
}; 