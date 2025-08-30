<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First check if columns exist and copy data
        $hasSubject = Schema::hasColumn('contact_messages', 'subject');
        $hasMessage = Schema::hasColumn('contact_messages', 'message');
        
        // If subject/message columns don't exist, create them
        if (!$hasSubject || !$hasMessage) {
            Schema::table('contact_messages', function (Blueprint $table) use ($hasSubject, $hasMessage) {
                if (!$hasSubject) {
                    $table->string('subject', 255)->default('')->after('company');
                }
                if (!$hasMessage) {
                    $table->text('message')->after($hasSubject ? 'subject' : 'company');
                }
            });
        }

        // Copy data from multilingual fields to single fields
        // Prefer Arabic if available, fallback to English
        if (Schema::hasColumn('contact_messages', 'subject_ar')) {
            DB::statement("
                UPDATE contact_messages 
                SET 
                    subject = COALESCE(NULLIF(subject_ar, ''), subject_en, ''),
                    message = COALESCE(NULLIF(message_ar, ''), message_en, '')
                WHERE (subject = '' OR subject IS NULL) OR (message = '' OR message IS NULL)
            ");
        }

        // Drop the multilingual columns if they exist
        $columnsToCheck = ['subject_ar', 'subject_en', 'message_ar', 'message_en'];
        $existingColumns = [];
        
        foreach ($columnsToCheck as $column) {
            if (Schema::hasColumn('contact_messages', $column)) {
                $existingColumns[] = $column;
            }
        }
        
        if (!empty($existingColumns)) {
            Schema::table('contact_messages', function (Blueprint $table) use ($existingColumns) {
                $table->dropColumn($existingColumns);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Step 1: Add back multilingual columns
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('subject_ar')->nullable()->after('company');
            $table->string('subject_en')->nullable()->after('subject_ar');
            $table->text('message_ar')->nullable()->after('subject_en');
            $table->text('message_en')->nullable()->after('message_ar');
        });

        // Step 2: Copy data back to multilingual fields
        DB::statement("
            UPDATE contact_messages 
            SET 
                subject_ar = subject,
                subject_en = subject,
                message_ar = message,
                message_en = message
        ");

        // Step 3: Drop the single language columns
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['subject', 'message']);
        });
    }
};
