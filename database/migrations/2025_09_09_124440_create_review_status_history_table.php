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
        Schema::create('review_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->enum('old_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->enum('new_status', ['pending', 'approved', 'rejected']);
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes for performance
            $table->index(['review_id']);
            $table->index(['created_at']);
            $table->index(['changed_by']);
            $table->index(['new_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_status_history');
    }
}; 