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
        // إزالة الحقول القديمة المكررة من categories
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'name')) {
                $table->dropColumn('name');
            }
        });

        // إزالة الحقول القديمة المكررة من products  
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('products', 'description')) {
                $table->dropColumn('description');
            }
        });

        // إزالة الحقول القديمة المكررة من brands (إذا كانت موجودة)
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                if (Schema::hasColumn('brands', 'name')) {
                    $table->dropColumn('name');
                }
                if (Schema::hasColumn('brands', 'description')) {
                    $table->dropColumn('description');
                }
            });
        }

        // إزالة الحقول القديمة المكررة من suppliers (إذا كانت موجودة)
        if (Schema::hasTable('suppliers')) {
            Schema::table('suppliers', function (Blueprint $table) {
                if (Schema::hasColumn('suppliers', 'name')) {
                    $table->dropColumn('name');
                }
                if (Schema::hasColumn('suppliers', 'description')) {
                    $table->dropColumn('description');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إعادة إضافة الحقول القديمة في حالة rollback
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->text('description')->nullable()->after('name');
        });

        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->string('name')->nullable()->after('id');
                $table->text('description')->nullable()->after('name');
            });
        }

        if (Schema::hasTable('suppliers')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->string('name')->nullable()->after('id');  
                $table->text('description')->nullable()->after('name');
            });
        }
    }
};
