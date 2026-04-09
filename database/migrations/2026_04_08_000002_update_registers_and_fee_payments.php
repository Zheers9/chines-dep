<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registers', function (Blueprint $table) {
            $table->renameColumn('paid_status', 'is_accepted');
        });

        Schema::table('fee_payments', function (Blueprint $table) {
            $table->foreignId('register_id')->nullable()->constrained('registers')->cascadeOnDelete();
            $table->string('voucher_num')->nullable();
            $table->text('comment')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->dropForeign(['register_id']);
            $table->dropColumn(['register_id', 'voucher_num', 'comment']);
        });

        Schema::table('registers', function (Blueprint $table) {
            $table->renameColumn('is_accepted', 'paid_status');
        });
    }
};
