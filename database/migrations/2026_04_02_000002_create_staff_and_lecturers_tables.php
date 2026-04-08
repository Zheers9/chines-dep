<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['lecturer', 'staff']);
            $table->string('name');
            $table->string('title')->nullable(); // Professor, Dean, Assistant
            $table->string('certificate')->nullable(); // PhD, Masters
            $table->string('role')->nullable(); // For staff: Admin, Coordinator
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('staff_gallery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_member_id')->constrained('staff_members')->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_gallery');
        Schema::dropIfExists('staff_members');
    }
};
