<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;   // Wajib ada biar DB::table jalan
use Illuminate\Support\Facades\Hash; // Wajib ada biar Hash::make jalan

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. MASTER DATA: AKUN SUPER ADMIN
        // Ini wajib ada biar kamu bisa login pertama kali
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@itbss.ac.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin'), // Passwordnya: admin
                'role' => 'admin',
                'student_id' => null, // Admin tidak punya NIM
                'major' => null,      // Admin tidak punya Jurusan
                'year' => null,       // Admin tidak punya Angkatan
                'updated_at' => now(),
            ]
        );

        // 2. MASTER DATA: SYSTEM SETTINGS
        // Aturan dasar aplikasi
        $settings = [
            ['key_name' => 'min_points_to_pass', 'value' => '20', 'data_type' => 'integer', 'description' => 'Minimum points required to pass S-Core', 'is_public' => true],
            ['key_name' => 'category_management_pin', 'value' => '123456', 'data_type' => 'string', 'description' => 'PIN for category management access', 'is_public' => false],
            ['key_name' => 'max_file_size_mb', 'value' => '10', 'data_type' => 'integer', 'description' => 'Maximum file upload size in MB', 'is_public' => true],
            ['key_name' => 'submission_date_range_months', 'value' => '1', 'data_type' => 'integer', 'description' => 'Activity date must be within X months', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['key_name' => $setting['key_name']],
                array_merge($setting, ['updated_at' => now()])
            );
        }

        // 3. MASTER DATA: KATEGORI & SUBKATEGORI
        // Wajib ada biar dropdown di form tidak kosong
        
        // Kategori 1: OrKeSS
        $cat1Id = DB::table('categories')->insertGetId([
            'name' => 'OrKeSS dan Retreat (WAJIB)',
            'is_mandatory' => true,
            'display_order' => 1,
            'created_at' => now(), 'updated_at' => now()
        ]);

        DB::table('subcategories')->insert([
            ['category_id' => $cat1Id, 'name' => 'OrKeSS (Orientasi Kemahasiswaan Sabda Setia)', 'points' => 1, 'description' => 'Per kegiatan', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $cat1Id, 'name' => 'Retreat', 'points' => 1, 'description' => 'Per kegiatan', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Kategori 2: Kegiatan Ilmiah
        $cat2Id = DB::table('categories')->insertGetId([
            'name' => 'Kegiatan Ilmiah dan Penalaran',
            'is_mandatory' => false,
            'display_order' => 2,
            'created_at' => now(), 'updated_at' => now()
        ]);

        DB::table('subcategories')->insert([
            ['category_id' => $cat2Id, 'name' => 'Magang/Kerja Praktek', 'points' => 20, 'description' => 'Minimal 1 bulan', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $cat2Id, 'name' => 'Program Kampus Merdeka', 'points' => 20, 'description' => 'Mengikuti program resmi Kemendikbud', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => $cat2Id, 'name' => 'HKI/Paten', 'points' => 20, 'description' => 'Per HKI yang terdaftar', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. SAMPLE DATA: AKUN MAHASISWA TEST
        $students = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@student.itbss.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'student_id' => '2021001',
                'major' => 'STI',
                'year' => 2021,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@student.itbss.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'student_id' => '2021002',
                'major' => 'BD',
                'year' => 2021,
            ],
            [
                'name' => 'Ahmad Wijaya',
                'email' => 'ahmad.wijaya@student.itbss.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'student_id' => '2022001',
                'major' => 'KWU',
                'year' => 2022,
            ],
            [
                'name' => 'Diana Kusuma',
                'email' => 'diana.kusuma@student.itbss.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'student_id' => '2022002',
                'major' => 'STI',
                'year' => 2022,
            ],
        ];

        foreach ($students as $student) {
            DB::table('users')->updateOrInsert(
                ['email' => $student['email']],
                array_merge($student, ['updated_at' => now()])
            );
        }
    }
}