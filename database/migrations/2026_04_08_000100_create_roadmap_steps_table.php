<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roadmap_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_sub_type_id')->constrained('exam_sub_types')->cascadeOnDelete();
            $table->foreignId('pre_node_id')->nullable()->constrained('roadmap_steps')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['resource', 'exam'])->default('exam');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->string('video_url')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('total_marks')->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->string('color')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roadmap_steps');
    }
};
