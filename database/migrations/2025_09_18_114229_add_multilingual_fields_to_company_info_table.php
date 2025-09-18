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
        Schema::table('company_info', function (Blueprint $table) {
            // إضافة الحقول متعددة اللغات
            $table->string('company_name_ar')->nullable()->after('company_name');
            $table->string('company_name_en')->nullable()->after('company_name_ar');
            $table->text('company_description_ar')->nullable()->after('company_description');
            $table->text('company_description_en')->nullable()->after('company_description_ar');
            $table->text('mission_ar')->nullable()->after('mission');
            $table->text('mission_en')->nullable()->after('mission_ar');
            $table->text('vision_ar')->nullable()->after('vision');
            $table->text('vision_en')->nullable()->after('vision_ar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_info', function (Blueprint $table) {
            $table->dropColumn([
                'company_name_ar',
                'company_name_en',
                'company_description_ar',
                'company_description_en',
                'mission_ar',
                'mission_en',
                'vision_ar',
                'vision_en'
            ]);
        });
    }
};
