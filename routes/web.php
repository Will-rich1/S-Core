<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController; // Pastikan UserController diimport

// --- JALUR LOGIN & LOGOUT ---
Route::get('/', [AuthController::class, 'showLoginForm']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// --- JALUR SETELAH LOGIN (Diproteksi Middleware) ---
Route::middleware(['auth'])->group(function () {

    // Jalur Mahasiswa
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Jalur Admin
    Route::get('/admin', function () {
        return view('admin_review');
    })->name('admin.review');

    // Jalur User Management
    // Students
    Route::post('/students/store', [UserController::class, 'storeStudent'])->name('students.store');
    Route::post('/students/import', [UserController::class, 'importStudents'])->name('students.import');
    
    // Admins
    Route::post('/admins/store', [UserController::class, 'storeAdmin'])->name('admins.store');

});

// ==========================================
// AREA TEST SESSION (Untuk Debugging Session)
// ==========================================
Route::get('/tes-simpan', function () {
    session(['uji_coba' => 'Berhasil! Session berfungsi.']);
    return 'Data session telah disimpan. <a href="/tes-baca">Klik di sini untuk membaca</a>';
});

Route::get('/tes-baca', function () {
    $data = session('uji_coba', 'GAGAL! Session kosong/hilang.');
    return 'Hasil Baca Session: <strong>' . $data . '</strong>';
});