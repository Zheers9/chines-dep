<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exams_roadmap_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_sub_type_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['resource', 'exam'])->default('exam');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->string('video_url')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('total_marks')->nullable();
            $table->integer('order')->default(0);
            $table->string('color')->nullable(); // for UI color coding
            $table->timestamps();
        });

        Schema::create('exams_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_step_id')->constrained('exams_roadmap_steps')->cascadeOnDelete();
            $table->enum('type', ['multiple_choice', 'blank', 'short_answer', 'sound_to_write']);
            $table->text('content'); // The question or sound transcription
            $table->string('audio_url')->nullable(); 
            $table->integer('weight')->default(1);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('exams_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('exams_questions')->cascadeOnDelete();
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        Schema::create('exams_question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('exams_questions')->cascadeOnDelete();
            $table->text('answer_text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams_question_answers');
        Schema::dropIfExists('exams_question_options');
        Schema::dropIfExists('exams_questions');
        Schema::dropIfExists('exams_roadmap_steps');
    }
};
