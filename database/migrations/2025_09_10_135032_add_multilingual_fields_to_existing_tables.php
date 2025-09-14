<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة حقول متعددة اللغات لجدول categories
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
            $table->string('name_en')->nullable()->after('name_ar');
            $table->text('description_ar')->nullable()->after('name_en');
            $table->text('description_en')->nullable()->after('description_ar');
            $table->string('image')->nullable()->after('description_en');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('image');
            $table->boolean('featured')->default(false)->after('status');
            $table->integer('sort_order')->default(0)->after('featured');
        });

        // إضافة حقول متعددة اللغات لجدول products
        Schema::table('products', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
            $table->string('name_en')->nullable()->after('name_ar');
            $table->text('description_ar')->nullable()->after('description');
            $table->text('description_en')->nullable()->after('description_ar');
            $table->string('sku')->nullable()->after('description_en');
            $table->decimal('sale_price', 10, 2)->nullable()->after('price');
            $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active')->after('sale_price');
            $table->boolean('featured')->default(false)->after('status');
            $table->integer('sort_order')->default(0)->after('featured');
            $table->json('specifications')->nullable()->after('sort_order');
            $table->json('features')->nullable()->after('specifications');
            $table->decimal('weight', 8, 2)->nullable()->after('features');
            $table->json('dimensions')->nullable()->after('weight');
        });

        // التأكد من وجود جدول suppliers وإضافة الحقول المطلوبة
        if (Schema::hasTable('suppliers')) {
            Schema::table('suppliers', function (Blueprint $table) {
                if (!Schema::hasColumn('suppliers', 'name_ar')) {
                    $table->string('name_ar')->nullable()->after('name');
                    $table->string('name_en')->nullable()->after('name_ar');
                    $table->text('description_ar')->nullable()->after('name_en');
                    $table->text('description_en')->nullable()->after('description_ar');
                    $table->string('contact_person')->nullable()->after('description_en');
                    $table->string('email')->nullable()->after('contact_person');
                    $table->string('phone')->nullable()->after('email');
                    $table->text('address')->nullable()->after('phone');
                    $table->string('city')->nullable()->after('address');
                    $table->string('country')->default('Egypt')->after('city');
                    $table->enum('status', ['active', 'inactive'])->default('active')->after('country');
                }
            });
        }

        // نسخ البيانات من name إلى name_ar و name_en
        DB::statement("UPDATE categories SET name_ar = name, name_en = name WHERE name_ar IS NULL");
        DB::statement("UPDATE products SET name_ar = name, name_en = name WHERE name_ar IS NULL");
        DB::statement("UPDATE products SET description_ar = description, description_en = description WHERE description_ar IS NULL");
        
        if (Schema::hasTable('suppliers')) {
            DB::statement("UPDATE suppliers SET name_ar = name, name_en = name WHERE name_ar IS NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar', 'name_en', 'description_ar', 'description_en',
                'image', 'status', 'featured', 'sort_order'
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'name_ar', 'name_en', 'description_ar', 'description_en',
                'sku', 'sale_price', 'status', 'featured', 'sort_order',
                'specifications', 'features', 'weight', 'dimensions'
            ]);
        });

        if (Schema::hasTable('suppliers') && Schema::hasColumn('suppliers', 'name_ar')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropColumn([
                    'name_ar', 'name_en', 'description_ar', 'description_en',
                    'contact_person', 'email', 'phone', 'address', 'city', 'country', 'status'
                ]);
        });
        }
    }
};
