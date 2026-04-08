<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_step_id')->constrained('roadmap_steps')->cascadeOnDelete();
            $table->enum('type', ['multiple_choice', 'blank', 'true_false', 'voice']);
            $table->text('content');
            $table->string('audio_url')->nullable();
            $table->unsignedInteger('weight')->default(5);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
