<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY academic_status ENUM('active', 'on_leave', 'graduated', 'non_active') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE users SET academic_status = 'active' WHERE academic_status = 'non_active'");
        DB::statement("ALTER TABLE users MODIFY academic_status ENUM('active', 'on_leave', 'graduated') NOT NULL DEFAULT 'active'");
    }
};
