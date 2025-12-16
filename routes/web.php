<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. JALUR TAMU (Belum Login) ---
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// --- 2. JALUR SETELAH LOGIN (Wajib Login) ---
Route::middleware(['auth'])->group(function () {
    
    // Logout & Ganti Password
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change');

    // --- AREA MAHASISWA ---
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'studentDashboard'])->name('dashboard');
    
    // Submissions (CRUD Lengkap)
    Route::post('/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
    
    // UPDATE: Menggunakan PUT untuk edit sesuai standar RESTful & JS kita
    Route::put('/submissions/{id}', [SubmissionController::class, 'update'])->name('submissions.update');
    
    // DELETE: Menggunakan DELETE untuk hapus sesuai standar RESTful & JS kita
    Route::delete('/submissions/{id}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');
    
    // COMPLAINT: Rute khusus untuk komplain
    Route::post('/submissions/complaint', [SubmissionController::class, 'storeComplaint'])->name('submissions.complaint');


    // --- AREA ADMIN ---
    Route::get('/admin', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    Route::post('/admin/submissions/{id}/approve', [SubmissionController::class, 'approve'])->name('admin.approve');
    Route::post('/admin/submissions/{id}/reject', [SubmissionController::class, 'reject'])->name('admin.reject');

    // User Management
    Route::post('/students/store', [UserController::class, 'storeStudent'])->name('students.store');
    Route::post('/students/import', [UserController::class, 'importStudents'])->name('students.import');
    Route::post('/admins/store', [UserController::class, 'storeAdmin'])->name('admins.store');
});