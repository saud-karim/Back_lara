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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->tinyInteger('rating')->unsigned()->default(1)->comment('Rating from 1 to 5');
            $table->text('review');
            $table->text('review_ar')->nullable();
            $table->text('review_en')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('verified_purchase')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->text('admin_response')->nullable();
            $table->foreignId('admin_response_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('admin_response_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['status']);
            $table->index(['rating']);
            $table->index(['product_id']);
            $table->index(['user_id']);
            $table->index(['created_at']);
            $table->index(['verified_purchase']);

            // Note: Rating validation will be handled by Laravel validation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
