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
        Schema::create('company_info', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('Construction Tools');
            $table->text('company_description')->nullable();
            $table->text('mission')->nullable();
            $table->text('vision')->nullable();
            $table->string('logo_text', 10)->default('BS');
            $table->string('founded_year', 10)->default('2009');
            $table->string('employees_count', 20)->default('50+');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_info');
    }
};
