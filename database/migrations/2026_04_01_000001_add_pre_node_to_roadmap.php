<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exams_roadmap_steps', function (Blueprint $table) {
            $table->foreignId('pre_node_id')->nullable()->after('exam_sub_type_id')->constrained('exams_roadmap_steps')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('exams_roadmap_steps', function (Blueprint $table) {
            $table->dropForeign(['pre_node_id']);
            $table->dropColumn('pre_node_id');
        });
    }
};
