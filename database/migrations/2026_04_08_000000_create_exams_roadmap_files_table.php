<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exams_roadmap_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_step_id')->constrained('exams_roadmap_steps')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('file_type'); // pdf, image, docx, etc.
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams_roadmap_files');
    }
};
