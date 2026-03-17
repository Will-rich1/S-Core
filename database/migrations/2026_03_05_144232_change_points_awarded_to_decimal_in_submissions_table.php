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
            $table->decimal('points_awarded', 8, 2)->nullable()->change();
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->decimal('points', 8, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->integer('points_awarded')->nullable()->change();
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->integer('points')->default(0)->change();
        });
    }
};
