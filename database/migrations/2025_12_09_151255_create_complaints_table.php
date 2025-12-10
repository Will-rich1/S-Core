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
    Schema::create('complaints', function (Blueprint $table) {
        $table->id();
        $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
        $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
        
        $table->enum('type', ['appeal_rejection', 'appeal_points', 'appeal_category', 'other']);
        $table->text('explanation');
        $table->string('supporting_document_path', 500)->nullable();
        
        $table->enum('status', ['pending', 'under_review', 'resolved', 'rejected'])->default('pending');
        $table->text('admin_response')->nullable();
        
        $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('reviewed_at')->nullable();
        
        $table->timestamps();
        
        $table->index('status'); // Sudah sesuai
        $table->index('submission_id');
        $table->index('student_id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
