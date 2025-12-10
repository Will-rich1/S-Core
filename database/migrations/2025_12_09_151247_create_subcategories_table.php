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
    Schema::create('subcategories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
        $table->string('name');
        $table->integer('points')->default(0);
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(true);
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();

        $table->index('points'); // Sudah sesuai
        $table->index('category_id'); // Foreign key index
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcategories');
    }
};
