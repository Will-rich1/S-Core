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
    // (Pengecekan role dilakukan di dalam DashboardController)
    Route::get('/dashboard', [DashboardController::class, 'studentDashboard'])->name('dashboard');
    
    Route::post('/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::post('/submissions/{id}/update', [SubmissionController::class, 'update'])->name('submissions.update');
    Route::post('/submissions/{id}/delete', [SubmissionController::class, 'destroy'])->name('submissions.delete');
    Route::post('/complaints', [SubmissionController::class, 'storeComplaint'])->name('complaints.store');

    // --- AREA ADMIN ---
    // (Pengecekan role dilakukan di dalam DashboardController)
    Route::get('/admin', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    Route::post('/admin/submissions/{id}/approve', [SubmissionController::class, 'approve'])->name('admin.approve');
    Route::post('/admin/submissions/{id}/reject', [SubmissionController::class, 'reject'])->name('admin.reject');

    // User Management
    Route::post('/students/store', [UserController::class, 'storeStudent'])->name('students.store');
    Route::post('/students/import', [UserController::class, 'importStudents'])->name('students.import');
    Route::post('/admins/store', [UserController::class, 'storeAdmin'])->name('admins.store');
});