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
        Schema::table('exams_sections', function (Blueprint $table) {
            $table->longText('passage')->nullable()->after('marks');
        });
    }

    public function down(): void
    {
        Schema::table('exams_sections', function (Blueprint $table) {
            $table->dropColumn('passage');
        });
    }
};
