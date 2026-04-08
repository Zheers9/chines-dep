<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_step_id')->constrained('exams_roadmap_steps')->cascadeOnDelete();
            $table->string('title');
            $table->string('type'); // common type for all questions in this section
            $table->integer('marks')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::table('exams_questions', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->constrained('exams_sections')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exams_questions', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });
        Schema::dropIfExists('exams_sections');
    }
};
