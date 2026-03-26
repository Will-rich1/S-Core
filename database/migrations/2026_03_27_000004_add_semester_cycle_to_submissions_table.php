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
        Schema::table('submissions', function (Blueprint $table) {
            $table->unsignedInteger('semester_cycle')
                ->default(0)
                ->after('activity_date');

            $table->index(['student_id', 'student_category_id', 'semester_cycle'], 'submissions_student_category_semester_cycle_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex('submissions_student_category_semester_cycle_idx');
            $table->dropColumn('semester_cycle');
        });
    }
};
