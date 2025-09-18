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
        Schema::create('company_milestones', function (Blueprint $table) {
            $table->id();
            $table->string('year', 10)->index();
            $table->string('event_ar')->index();
            $table->string('event_en')->index();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->integer('order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_milestones');
    }
};
