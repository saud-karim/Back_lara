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
        Schema::create('company_stats', function (Blueprint $table) {
            $table->id();
            $table->string('years_experience', 20)->default('15+');
            $table->string('total_customers', 20)->default('50K+');
            $table->string('completed_projects', 20)->default('1000+');
            $table->string('support_availability', 20)->default('24/7');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_stats');
    }
};
