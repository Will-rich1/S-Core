<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SCoreReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FileProxyController;
use App\Http\Controllers\AdminIndividualScoreController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

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

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
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
    
    // UPDATE: Mahasiswa hanya bisa edit submission berstatus Rejected (dicek di controller)
    Route::put('/submissions/{id}', [SubmissionController::class, 'update'])->name('submissions.update');
    
    // DELETE: Menggunakan DELETE untuk hapus sesuai standar RESTful & JS kita
    Route::delete('/submissions/{id}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');
    
    // COMPLAINT: Rute khusus untuk komplain
    Route::post('/submissions/complaint', [SubmissionController::class, 'storeComplaint'])->name('submissions.complaint');

    // Update Password Profile
    Route::post('/profile/update-password', [DashboardController::class, 'updatePassword'])->name('profile.update-password');


    // --- AREA ADMIN ---
    Route::get('/admin', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/admin/master-data', [DashboardController::class, 'adminMasterData'])->name('admin.master-data');
    Route::get('/admin/perfect-data', [DashboardController::class, 'adminPerfectData'])->name('admin.perfect-data');
    Route::get('/admin/students/{studentId}/detail', [DashboardController::class, 'adminStudentDetail'])->name('admin.students.detail');

    // Approval / Rejection
    Route::post('/admin/submissions/{id}/approve', [SubmissionController::class, 'approve'])->name('admin.approve');
    Route::post('/admin/submissions/{id}/reject', [SubmissionController::class, 'reject'])->name('admin.reject');
    Route::post('/admin/submissions/{id}/update-category', [SubmissionController::class, 'updateCategory'])->name('admin.submissions.update-category');
    Route::post('/admin/submissions/{id}/adjust-points', [SubmissionController::class, 'adjustPoints'])->name('admin.submissions.adjust-points');
    Route::delete('/admin/submissions/{id}', [SubmissionController::class, 'adminDestroy'])->name('admin.submissions.destroy');
    Route::put('/admin/submissions/{id}/edit', [SubmissionController::class, 'adminEdit'])->name('admin.submissions.edit');
    Route::post('/admin/submissions/{id}/admin-edit', [SubmissionController::class, 'adminEdit'])->name('admin.submissions.admin-edit');

    // User Management
    Route::post('/students/store', [UserController::class, 'storeStudent'])->name('students.store');
    Route::post('/students/import', [UserController::class, 'importStudents'])->name('students.import');
    Route::post('/admins/store', [UserController::class, 'storeAdmin'])->name('admins.store');
    Route::post('/admin/students/delete', [UserController::class, 'deleteStudents'])->name('students.delete');
    Route::post('/admin/students/promote-semester', [UserController::class, 'promoteSemester'])->name('students.promote-semester');
    Route::post('/admin/students/demote-semester', [UserController::class, 'demoteSemester'])->name('students.demote-semester');
    Route::post('/admin/students/{studentId}/academic-status', [UserController::class, 'updateAcademicStatus'])->name('students.update-academic-status');
    Route::post('/admin/students/academic-status/bulk', [UserController::class, 'bulkUpdateAcademicStatus'])->name('students.bulk-update-academic-status');
    Route::post('/admin/students/{studentId}/reset-points', [UserController::class, 'resetStudentPoints'])->name('students.reset-points');
    // Admin: reset user password
    Route::post('/admin/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Bulk Score
    Route::post('/admin/bulk-score', [DashboardController::class, 'bulkScore'])->name('admin.bulk-score');
    Route::post('/admin/students/{studentId}/individual-score', [AdminIndividualScoreController::class, 'store'])->name('admin.students.individual-score');

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

    // --- S-CORE SETTINGS ROUTES ---
    Route::get('/api/settings/score', [SettingsController::class, 'getScoreSettings'])->name('settings.score.get');
    Route::post('/admin/settings/score', [SettingsController::class, 'updateScoreSettings'])->name('settings.score.update');
    Route::post('/admin/settings/perfect-points', [SettingsController::class, 'updatePerfectPoints'])->name('settings.perfect-points.update');
    Route::get('/api/admin/settings', [SettingsController::class, 'getAllSettings'])->name('settings.all');

    // --- SECURITY PIN ROUTES ---
    Route::get('/api/settings/security-pin', [SettingsController::class, 'getSecurityPin'])->name('settings.pin.get');
    Route::post('/admin/settings/security-pin', [SettingsController::class, 'updateSecurityPin'])->name('settings.pin.update');
    Route::post('/api/verify-security-pin', [SettingsController::class, 'verifySecurityPin'])->name('settings.pin.verify');
    Route::post('/admin/reset-pin', [SettingsController::class, 'resetPin'])->name('admin.reset-pin');

    // --- S-CORE REPORT ROUTES ---
    Route::get('/student/{student_id}/report/view', [SCoreReportController::class, 'viewReport'])->name('student.report.view');
    Route::get('/student/{student_id}/report', [SCoreReportController::class, 'downloadReport'])->name('student.report.download');
    Route::get('/student/{student_id}/report/check', [SCoreReportController::class, 'checkEligibility'])->name('student.report.check');
    Route::get('/student/{student_id}/status', [SCoreReportController::class, 'getStatus'])->name('student.status');

    // File preview/download proxy (Google Drive/local)
    Route::get('/submissions/{submissionId}/file', [FileProxyController::class, 'serveFile'])->name('submissions.file.preview');

});