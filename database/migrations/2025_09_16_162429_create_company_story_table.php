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
        Schema::create('company_story', function (Blueprint $table) {
            $table->id();
            $table->text('paragraph1_ar')->nullable();
            $table->text('paragraph1_en')->nullable();
            $table->text('paragraph2_ar')->nullable();
            $table->text('paragraph2_en')->nullable();
            $table->text('paragraph3_ar')->nullable();
            $table->text('paragraph3_en')->nullable();
            $table->json('features')->nullable(); // Array of features with ar/en names
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_story');
    }
};
