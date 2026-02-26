<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('type_exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('exam_sub_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('type_exam_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
        Schema::create('exam_sub_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('exam_sub_type_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_sub_levels');
        Schema::dropIfExists('exam_sub_types');
        Schema::dropIfExists('type_exams');
    }
};
