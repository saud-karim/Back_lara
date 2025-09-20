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
        Schema::table('contact_info', function (Blueprint $table) {
            $table->text('google_maps_url')->nullable()->after('toll_free_label_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_info', function (Blueprint $table) {
            $table->dropColumn('google_maps_url');
        });
    }
};
