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
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate()->comment('fee');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate()->comment('user');
            $table->foreignId('exam_sub_type_id')->nullable()->constrained('exam_sub_types')->nullOnDelete()->cascadeOnUpdate()->comment('exam sub type');
            $table->string('pay')->nullable()->comment('payment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};
