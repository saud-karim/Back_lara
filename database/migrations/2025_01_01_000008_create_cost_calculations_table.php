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
        Schema::create('cost_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('area', 10, 2)->nullable();
            $table->json('materials'); // Array of product IDs and quantities
            $table->decimal('total_cost', 10, 2);
            $table->timestamps();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('total_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_calculations');
    }
}; 