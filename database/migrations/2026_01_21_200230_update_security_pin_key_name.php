<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if security_pin already exists
        $securityPinExists = DB::table('system_settings')
            ->where('key_name', 'security_pin')
            ->exists();
        
        // Check if category_management_pin exists
        $categoryPinExists = DB::table('system_settings')
            ->where('key_name', 'category_management_pin')
            ->exists();
        
        if ($securityPinExists && $categoryPinExists) {
            // Both exist, delete the old one (category_management_pin)
            DB::table('system_settings')
                ->where('key_name', 'category_management_pin')
                ->delete();
        } elseif (!$securityPinExists && $categoryPinExists) {
            // Only old one exists, rename it
            DB::table('system_settings')
                ->where('key_name', 'category_management_pin')
                ->update(['key_name' => 'security_pin']);
        } elseif (!$securityPinExists && !$categoryPinExists) {
            // Neither exists, create security_pin with default value
            DB::table('system_settings')->insert([
                'key_name' => 'security_pin',
                'value' => '123456',
                'data_type' => 'string',
                'description' => 'Security PIN for category management',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        // If only security_pin exists, do nothing (already correct)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: change security_pin back to category_management_pin
        DB::table('system_settings')
            ->where('key_name', 'security_pin')
            ->update(['key_name' => 'category_management_pin']);
    }
};
