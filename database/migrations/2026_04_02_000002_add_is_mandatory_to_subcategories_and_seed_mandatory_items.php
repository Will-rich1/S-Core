<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('subcategories', 'is_mandatory')) {
            Schema::table('subcategories', function (Blueprint $table) {
                $table->boolean('is_mandatory')->default(false)->after('description');
            });
        }

        DB::table('categories')
            ->where(function ($query) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%orkess%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%retreat%']);
            })
            ->update(['is_mandatory' => true]);

        if (Schema::hasColumn('subcategories', 'is_mandatory')) {
            DB::table('subcategories')
                ->whereRaw('LOWER(name) = ?', [strtolower('Penguasaan Bahasa Inggris Aktif (ITP TOEFL 450 atau setara) (WAJIB)')])
                ->update(['is_mandatory' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('subcategories', 'is_mandatory')) {
            DB::table('subcategories')
                ->whereRaw('LOWER(name) = ?', [strtolower('Penguasaan Bahasa Inggris Aktif (ITP TOEFL 450 atau setara) (WAJIB)')])
                ->update(['is_mandatory' => false]);

            Schema::table('subcategories', function (Blueprint $table) {
                $table->dropColumn('is_mandatory');
            });
        }

        DB::table('categories')
            ->where(function ($query) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%orkess%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%retreat%']);
            })
            ->update(['is_mandatory' => false]);
    }
};
