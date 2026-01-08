<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SCoreReportController;

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

    // Update Password Profile
    Route::post('/profile/update-password', [DashboardController::class, 'updatePassword'])->name('profile.update-password');


    // --- AREA ADMIN ---
    Route::get('/admin', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    // Approval / Rejection
    Route::post('/admin/submissions/{id}/approve', [SubmissionController::class, 'approve'])->name('admin.approve');
    Route::post('/admin/submissions/{id}/reject', [SubmissionController::class, 'reject'])->name('admin.reject');
    Route::put('/admin/submissions/{id}/edit', [SubmissionController::class, 'adminEdit'])->name('admin.submissions.edit');
    Route::post('/admin/submissions/{id}/admin-edit', [SubmissionController::class, 'adminEdit'])->name('admin.submissions.admin-edit');

    // User Management
    Route::post('/students/store', [UserController::class, 'storeStudent'])->name('students.store');
    Route::post('/students/import', [UserController::class, 'importStudents'])->name('students.import');
    Route::post('/admins/store', [UserController::class, 'storeAdmin'])->name('admins.store');
    // Admin: reset user password
    Route::post('/admin/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Bulk Score
    Route::post('/admin/bulk-score', [DashboardController::class, 'bulkScore'])->name('admin.bulk-score');

    // --- CATEGORY MANAGEMENT (BARU) ---
    // API ENDPOINTS - Get Categories (READ ONLY)
    Route::get('/api/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/api/categories/student', [CategoryController::class, 'studentCategories'])->name('categories.student');
    Route::post('/admin/categories/{id}/reactivate', [CategoryController::class, 'reactivate'])->name('categories.reactivate');
    Route::post('/admin/subcategories/{id}/reactivate', [CategoryController::class, 'reactivateSubcategory'])->name('subcategories.reactivate');
    
    // Main Categories - POST/PUT/DELETE
    Route::post('/admin/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/admin/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/admin/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');


    // SUBCATEGORY ROUTES
    Route::post('/admin/subcategories', [CategoryController::class, 'storeSubcategory'])->name('subcategories.store');
    Route::put('/admin/subcategories/{id}', [CategoryController::class, 'updateSubcategory'])->name('subcategories.update'); // <-- Pastikan ini ada
    Route::delete('/admin/subcategories/{id}', [CategoryController::class, 'destroySubcategory'])->name('subcategories.destroy'); // <-- Pastikan ini ada

    // --- S-CORE REPORT ROUTES ---
    Route::get('/student/{student_id}/report', [SCoreReportController::class, 'downloadReport'])->name('student.report.download');
    Route::get('/student/{student_id}/report/check', [SCoreReportController::class, 'checkEligibility'])->name('student.report.check');
    Route::get('/student/{student_id}/status', [SCoreReportController::class, 'getStatus'])->name('student.status');

});