<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams_user_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('roadmap_step_id')->constrained('exams_roadmap_steps')->onDelete('cascade');
            $table->integer('score')->nullable();
            $table->string('status')->default('completed'); // or 'started'
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'roadmap_step_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams_user_progress');
    }
};
