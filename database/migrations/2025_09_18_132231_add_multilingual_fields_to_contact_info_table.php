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
            // Address fields - multilingual
            $table->string('address_street_ar')->nullable()->after('address_country');
            $table->string('address_street_en')->nullable()->after('address_street_ar');
            $table->string('address_district_ar')->nullable()->after('address_street_en');
            $table->string('address_district_en')->nullable()->after('address_district_ar');
            $table->string('address_city_ar')->nullable()->after('address_district_en');
            $table->string('address_city_en')->nullable()->after('address_city_ar');
            $table->string('address_country_ar')->nullable()->after('address_city_en');
            $table->string('address_country_en')->nullable()->after('address_country_ar');
            
            // Working hours fields - multilingual
            $table->text('working_hours_weekdays_ar')->nullable()->after('working_hours_saturday');
            $table->text('working_hours_weekdays_en')->nullable()->after('working_hours_weekdays_ar');
            $table->text('working_hours_friday_ar')->nullable()->after('working_hours_weekdays_en');
            $table->text('working_hours_friday_en')->nullable()->after('working_hours_friday_ar');
            $table->text('working_hours_saturday_ar')->nullable()->after('working_hours_friday_en');
            $table->text('working_hours_saturday_en')->nullable()->after('working_hours_saturday_ar');
            
            // WhatsApp field
            $table->string('whatsapp')->nullable()->after('working_hours_saturday_en');
            
            // Additional contact fields - multilingual
            $table->string('emergency_phone_label_ar')->nullable()->after('whatsapp');
            $table->string('emergency_phone_label_en')->nullable()->after('emergency_phone_label_ar');
            $table->string('toll_free_label_ar')->nullable()->after('emergency_phone_label_en');
            $table->string('toll_free_label_en')->nullable()->after('toll_free_label_ar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_info', function (Blueprint $table) {
            $table->dropColumn([
                'address_street_ar',
                'address_street_en',
                'address_district_ar', 
                'address_district_en',
                'address_city_ar',
                'address_city_en',
                'address_country_ar',
                'address_country_en',
                'working_hours_weekdays_ar',
                'working_hours_weekdays_en',
                'working_hours_friday_ar',
                'working_hours_friday_en',
                'working_hours_saturday_ar',
                'working_hours_saturday_en',
                'emergency_phone_label_ar',
                'emergency_phone_label_en',
                'toll_free_label_ar',
                'toll_free_label_en'
            ]);
        });
    }
};
