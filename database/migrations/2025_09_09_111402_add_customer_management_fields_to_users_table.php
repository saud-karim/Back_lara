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
        Schema::table('users', function (Blueprint $table) {
            // إضافة حقول إدارة العملاء
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
            $table->timestamp('last_activity')->nullable();
            $table->string('avatar')->nullable();
            $table->string('registration_source', 50)->default('website');
            
            // إضافة indexes للبحث والفلترة
            $table->index(['status']);
            $table->index(['last_activity']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['last_activity']);
            $table->dropIndex(['created_at']);
            
            $table->dropColumn([
                'status',
                'last_activity', 
                'avatar',
                'registration_source'
            ]);
        });
    }
};
