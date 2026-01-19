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
            ['email' => 's-core.mahasiswa@itbss.ac.id'],
            [
                'name' => 'Kemahasiswaan ITBSS',
                'password' => Hash::make('scoremahasiswa123'), // Passwordnya: admin
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
        // Disusun sesuai daftar pada foto (a-f)
        $categories = [
            [
                'name' => 'OrKeSS (WAJIB)',
                'is_mandatory' => true,
                'display_order' => 1,
                'subcategories' => [
                    ['name' => 'OrKeSS', 'points' => 1, 'description' => 'Per Kegiatan'],
                ],
            ],
            [
                'name' => 'Retreat (WAJIB)',
                'is_mandatory' => true,
                'display_order' => 2,
                'subcategories' => [
                    ['name' => 'Retreat', 'points' => 1, 'description' => 'Per Kegiatan'],
                ],
            ],
            [
                'name' => 'Kegiatan Ilmiah dan Penalaran',
                'is_mandatory' => false,
                'display_order' => 3,
                'subcategories' => [
                    ['name' => 'Penguasaan Bahasa Inggris Aktif (ITP TOEFL 450 atau setara) (WAJIB)', 'points' => 2, 'description' => 'Per bahasa'],
                    ['name' => 'Penguasaan Bahasa Mandarin Aktif (HSK setara 4)', 'points' => 2, 'description' => 'Per bahasa'],
                    ['name' => 'Penguasaan Bahasa Asing lain', 'points' => 2, 'description' => 'Per bahasa'],
                    ['name' => 'Peningkatan kemampuan ilmiah dan penalaran - Peserta seminar, kuliah umum, dan sejenisnya', 'points' => 1, 'description' => 'Per kegiatan'],
                    ['name' => 'Peningkatan kemampuan ilmiah dan penalaran - Peserta pelatihan, workshop, lokakarya, upgrading program, dan sejenisnya', 'points' => 3, 'description' => 'Per kegiatan'],
                    ['name' => 'Peningkatan kemampuan ilmiah dan penalaran - Menyelesaikan online course/bootcamp bersertifikat', 'points' => 3, 'description' => 'Per course'],
                    ['name' => 'Pemakalah/Pemateri/Presenter/Trainer - Seminar/workshop kota/provinsi', 'points' => 6, 'description' => 'Per kegiatan'],
                    ['name' => 'Pemakalah/Pemateri/Presenter/Trainer - Seminar/workshop nasional', 'points' => 8, 'description' => 'Per kegiatan'],
                    ['name' => 'Pemakalah/Pemateri/Presenter/Trainer - Seminar/workshop internasional', 'points' => 10, 'description' => 'Per kegiatan'],
                    ['name' => 'Publikasi tulisan/karya di media massa bereputasi (online/offline)', 'points' => 2, 'description' => 'Per karya'],
                    ['name' => 'Publikasi tulisan/karya di media massa bereputasi (online/offline) dalam Bahasa Asing', 'points' => 4, 'description' => 'Per karya'],
                    ['name' => 'Asistensi dosen/lab', 'points' => 3, 'description' => 'Per semester'],
                    ['name' => 'Tenaga pengajar sukarela', 'points' => 2, 'description' => 'Per kegiatan'],
                ],
            ],
            [
                'name' => 'Performance, Pengembangan, dan Perlombaan',
                'is_mandatory' => false,
                'display_order' => 4,
                'subcategories' => [
                    ['name' => 'Peserta lomba - Tingkat lokal', 'points' => 2, 'description' => 'Per lomba'],
                    ['name' => 'Peserta lomba - Tingkat kota/provinsi', 'points' => 3, 'description' => 'Per lomba'],
                    ['name' => 'Peserta lomba - Tingkat nasional', 'points' => 4, 'description' => 'Per lomba'],
                    ['name' => 'Peserta lomba - Tingkat internasional', 'points' => 5, 'description' => 'Per lomba'],
                    ['name' => 'Pemenang lomba - Tingkat lokal', 'points' => 3, 'description' => 'Per lomba'],
                    ['name' => 'Pemenang lomba - Tingkat kota/provinsi', 'points' => 5, 'description' => 'Per lomba'],
                    ['name' => 'Pemenang lomba - Tingkat nasional', 'points' => 8, 'description' => 'Per lomba'],
                    ['name' => 'Pemenang lomba - Tingkat internasional', 'points' => 10, 'description' => 'Per lomba'],
                    ['name' => 'Parade budaya/seni - Tingkat lokal', 'points' => 1, 'description' => 'Per kegiatan'],
                    ['name' => 'Parade budaya/seni - Tingkat kota/provinsi', 'points' => 3, 'description' => 'Per kegiatan'],
                    ['name' => 'Parade budaya/seni - Tingkat nasional', 'points' => 5, 'description' => 'Per kegiatan'],
                    ['name' => 'Parade budaya/seni - Tingkat internasional', 'points' => 8, 'description' => 'Per kegiatan'],
                ],
            ],
            [
                'name' => 'Kepengurusan Organisasi/Kepanitiaan',
                'is_mandatory' => false,
                'display_order' => 5,
                'subcategories' => [
                    ['name' => 'Organisasi Kemahasiswaan - Pengurus Harian (Ketua, Wakil Ketua, Sekretaris, Bendahara)', 'points' => 5, 'description' => 'Per periode kepengurusan'],
                    ['name' => 'Organisasi Kemahasiswaan - Koordinator/Anggota Seksi', 'points' => 3, 'description' => 'Per periode kepengurusan'],
                    ['name' => 'Organisasi Kemahasiswaan - Anggota', 'points' => 1, 'description' => 'Per periode kepengurusan'],
                    ['name' => 'Organisasi di luar kampus - Pengurus Harian (Ketua, Wakil Ketua, Sekretaris, Bendahara)', 'points' => 4, 'description' => 'Per periode kepengurusan'],
                    ['name' => 'Organisasi di luar kampus - Koordinator/Anggota Seksi', 'points' => 2, 'description' => 'Per periode kepengurusan'],
                    ['name' => 'Organisasi di luar kampus - Anggota', 'points' => 1, 'description' => 'Per periode kepengurusan'],                   
                ],
            ],
            [
                'name' => 'Kegiatan Sosial Kemasyarakatan',
                'is_mandatory' => false,
                'display_order' => 6,
                'subcategories' => [
                    ['name' => 'Internship di badan bereputasi nasional', 'points' => 6, 'description' => 'Per periode'],
                    ['name' => 'Internship di badan bereputasi internasional', 'points' => 10, 'description' => 'Per periode'],
                    ['name' => 'Kegiatan bakti sosial', 'points' => 1, 'description' => 'Per kegiatan'],
                    ['name' => 'Social campaign (online/offline) pribadi', 'points' => 2, 'description' => 'Per karya'],
                    ['name' => 'Social campaign (online/offline) sebagai bagian kegiatan dari badan berputasi', 'points' => 4, 'description' => 'Per karya'],
                    ['name' => 'Mengembangkan hal bermanfaat bagi masyarakat (pembuatan aplikasi/produk) yang diakui dan digunakan oleh masyarakat', 'points' => 5, 'description' => 'Per karya'],
                ],
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category['name']],
                [
                    'is_mandatory' => $category['is_mandatory'],
                    'display_order' => $category['display_order'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $categoryId = DB::table('categories')->where('name', $category['name'])->value('id');
            if (!$categoryId) {
                continue;
            }

            foreach ($category['subcategories'] as $sub) {
                DB::table('subcategories')->updateOrInsert(
                    ['category_id' => $categoryId, 'name' => $sub['name']],
                    [
                        'points' => $sub['points'],
                        'description' => $sub['description'],
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

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