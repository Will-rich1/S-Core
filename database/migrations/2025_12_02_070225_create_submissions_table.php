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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_id');
            $table->string('student_name');
            $table->string('major');
            $table->string('kategori');
            $table->string('judul');
            $table->text('keterangan');
            $table->integer('point')->nullable();
            $table->integer('suggested_point');
            $table->date('activity_date');
            $table->string('certificate')->nullable();
            $table->enum('status', ['Waiting', 'Approved', 'Rejected'])->default('Waiting');
            $table->text('reject_reason')->nullable();
            $table->text('category_change_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
