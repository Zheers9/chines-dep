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
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate()->comment('exam sub type');
            $table->foreignId('exam_sub_type_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate()->comment('exam sub type');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate()->comment('user');
            $table->string('payment_amount')->nullable()->comment('payment amount'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
