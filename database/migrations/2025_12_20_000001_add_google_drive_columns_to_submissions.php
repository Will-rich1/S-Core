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
            // Tambah kolom untuk menyimpan URL public dan tipe storage
            $table->text('certificate_url')->nullable()->after('certificate_path');
            $table->string('storage_type', 20)->default('local')->after('certificate_url'); // 'local' or 'google'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['certificate_url', 'storage_type']);
        });
    }
};
