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
        $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
        
        // Kategori Pilihan Mahasiswa
        $table->foreignId('student_category_id')->constrained('categories');
        $table->foreignId('student_subcategory_id')->constrained('subcategories');
        
        // Kategori Assign Admin (Bisa Null)
        $table->foreignId('assigned_category_id')->nullable()->constrained('categories');
        $table->foreignId('assigned_subcategory_id')->nullable()->constrained('subcategories');
        
        // Detail
        $table->string('title', 500);
        $table->text('description');
        $table->date('activity_date');
        
        // File
        $table->string('certificate_path', 500)->nullable();
        $table->string('certificate_original_name')->nullable();
        
        // Status & Approval
        $table->enum('status', ['Waiting', 'Approved', 'Rejected', 'Cancel'])->default('Waiting');
        $table->integer('points_awarded')->nullable();
        
        // Rejection / Changes
        $table->text('rejection_reason')->nullable();
        $table->enum('rejection_type', ['certificate_invalid', 'duplicate', 'incomplete', 'other'])->nullable();
        $table->text('category_change_reason')->nullable();
        
        // Review Info
        $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('reviewed_at')->nullable();
        
        $table->timestamp('submitted_at')->useCurrent();
        $table->timestamps();

        // --- UPDATE: INDEX DIPISAH (SESUAI SQL) ---
        $table->index('status');
        $table->index('activity_date');
        $table->index('submitted_at');
        // Index foreign key biasanya otomatis, tapi kalau mau eksplisit boleh:
        $table->index('student_id'); 
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
