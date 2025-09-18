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
        Schema::create('contact_info', function (Blueprint $table) {
            $table->id();
            $table->string('main_phone', 20)->nullable();
            $table->string('secondary_phone', 20)->nullable();
            $table->string('toll_free', 100)->nullable();
            $table->string('main_email', 100)->nullable();
            $table->string('sales_email', 100)->nullable();
            $table->string('support_email', 100)->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_district', 100)->nullable();
            $table->string('address_city', 100)->nullable();
            $table->string('address_country', 100)->nullable();
            $table->string('working_hours_weekdays')->nullable();
            $table->string('working_hours_friday')->nullable();
            $table->string('working_hours_saturday')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_info');
    }
};
