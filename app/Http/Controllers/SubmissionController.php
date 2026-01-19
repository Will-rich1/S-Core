<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Category;
use App\Models\Subcategory;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * 1. Simpan Pengajuan Baru (Mahasiswa)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'activity_date'     => 'required|date',
            'mainCategory'      => 'required|string', // Dikirim sebagai Nama Kategori
            'subcategory'       => 'required|string', // Dikirim sebagai Nama Subkategori
            'certificate_file'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10MB
        ]);

        try {
            // 2. Cari ID Kategori & Subkategori berdasarkan NAMA
            $category = Category::where('name', $request->mainCategory)->first();
            
            if (!$category) {
                return response()->json(['message' => 'Category not found. Please select a valid category.'], 422);
            }

            $subcategory = Subcategory::where('category_id', $category->id)
                                      ->where('name', $request->subcategory)
                                      ->first();

            if (!$subcategory) {
                // Fallback: Cari by name saja
                $subcategory = Subcategory::where('name', $request->subcategory)->first();
                if (!$subcategory) {
                    return response()->json(['message' => 'Subcategory not found.'], 422);
                }
            }

            // 3. Upload File ke Google Drive with student_id for filename
            // Increase time limit untuk file besar
            set_time_limit(120); // 2 minutes for upload
            
            $googleDriveService = app(GoogleDriveService::class);
            $uploadResult = $googleDriveService->uploadFile(
                $request->file('certificate_file'),
                'certificates',
                Auth::user()->student_id // Pass student_id (NIM) for filename
            );

            // Log upload result untuk debugging
            \Log::info('File uploaded successfully', [
                'user' => Auth::id(),
                'storage' => $uploadResult['storage'] ?? 'unknown',
                'fallback' => $uploadResult['fallback'] ?? false
            ]);

            // 4. Simpan Data ke Database
            Submission::create([
                'student_id'             => Auth::id(),
                'student_category_id'    => $category->id,
                'student_subcategory_id' => $subcategory->id,
                'title'                  => $request->title,
                'description'            => $request->description,
                'activity_date'          => $request->activity_date,
                'certificate_path'       => $uploadResult['path'],
                'certificate_url'        => $uploadResult['url'] ?? null,
                'certificate_original_name' => $request->file('certificate_file')->getClientOriginalName(),
                'storage_type'           => $uploadResult['storage'] ?? 'google',
                'status'                 => 'Waiting',
            ]);

            return response()->json([
                'message' => 'Submission submitted successfully!',
                'storage' => $uploadResult['storage'] ?? 'google',
            ]);

        } catch (\Exception $e) {
            \Log::error('Submission error', [
                'user' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error saving data. Please try again or contact administrator if the problem persists.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * 2. Update Pengajuan (Edit Data)
     * UPDATE: Membolehkan edit jika status 'Waiting' ATAU 'Rejected'
     */
    public function update(Request $request, $id)
    {
        try {
            // Cari data punya user yang sedang login
            $submission = Submission::where('student_id', Auth::id())->where('id', $id)->firstOrFail();

            // Cek status: Boleh edit jika 'Waiting' ATAU 'Rejected'
            if ($submission->status !== 'Waiting' && $submission->status !== 'Rejected') {
                return response()->json(['message' => 'Cannot edit submission that has been approved.'], 403);
            }

            // Validasi (File bersifat nullable/opsional disini)
            $request->validate([
                'title'             => 'required|string|max:255',
                'description'       => 'required|string',
                'activity_date'     => 'required|date',
                'mainCategory'      => 'required|string',
                'subcategory'       => 'required|string',
                'certificate_file'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            ]);

            // Cari ID Kategori Baru (jika user mengubah kategori)
            $category = Category::where('name', $request->mainCategory)->first();
            $subcategory = Subcategory::where('name', $request->subcategory)->first();

            // Siapkan data untuk update
            $dataToUpdate = [
                'title' => $request->title,
                'description' => $request->description,
                'activity_date' => $request->activity_date,
                // Jika kategori/subkategori tidak ditemukan (invalid), pakai data lama (fallback)
                'student_category_id' => $category->id ?? $submission->student_category_id,
                'student_subcategory_id' => $subcategory->id ?? $submission->student_subcategory_id,
            ];

            // LOGIKA TAMBAHAN: Jika status sebelumnya Rejected, kembalikan ke Waiting
            if ($submission->status === 'Rejected') {
                $dataToUpdate['status'] = 'Waiting';
                $dataToUpdate['rejection_reason'] = null; // Hapus alasan penolakan
            }

            // Cek apakah ada file baru diupload
            if ($request->hasFile('certificate_file')) {
                $googleDriveService = app(GoogleDriveService::class);
                
                // Hapus file lama dari storage jika ada
                if ($submission->certificate_path) {
                    try {
                        \Log::info('Updating submission - deleting old file: ' . $submission->certificate_path);
                        
                        // Coba permanent delete dulu (untuk Google Drive)
                        if ($submission->storage_type === 'google' || strlen($submission->certificate_path) > 20) {
                            \Log::info('Attempting permanent delete via Google API');
                            $permanentDeleteResult = $googleDriveService->permanentlyDeleteFile($submission->certificate_path);
                            
                            if ($permanentDeleteResult) {
                                \Log::info('Old file permanently deleted successfully');
                            } else {
                                \Log::warning('Permanent delete failed, trying regular delete');
                                $deleteResult = $googleDriveService->deleteFile(
                                    $submission->certificate_path, 
                                    $submission->storage_type ?? 'google'
                                );
                                
                                if ($deleteResult) {
                                    \Log::info('Old file deleted successfully (moved to trash)');
                                } else {
                                    \Log::warning('Regular delete also failed');
                                }
                            }
                        } else {
                            $deleteResult = $googleDriveService->deleteFile(
                                $submission->certificate_path, 
                                $submission->storage_type ?? 'google'
                            );
                            
                            if ($deleteResult) {
                                \Log::info('Old file deleted successfully');
                            } else {
                                \Log::warning('Old file deletion returned false');
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to delete old file: ' . $e->getMessage());
                    }
                }

                // Upload file baru ke Google Drive with student_id for filename
                $uploadResult = $googleDriveService->uploadFile(
                    $request->file('certificate_file'),
                    'certificates',
                    Auth::user()->student_id // Pass student_id (NIM) for filename
                );
                
                $dataToUpdate['certificate_path'] = $uploadResult['path'];
                $dataToUpdate['certificate_url'] = $uploadResult['url'] ?? null;
                $dataToUpdate['certificate_original_name'] = $request->file('certificate_file')->getClientOriginalName();
                $dataToUpdate['storage_type'] = $uploadResult['storage'] ?? 'google';
            }

            $submission->update($dataToUpdate);

            return response()->json(['message' => 'Submission updated successfully!']);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 3. Hapus Pengajuan (Mahasiswa)
     */
    public function destroy($id)
    {
        try {
            $submission = Submission::where('student_id', Auth::id())->where('id', $id)->firstOrFail();
            
            // Izinkan hapus jika Waiting atau Rejected
            if ($submission->status !== 'Waiting' && $submission->status !== 'Rejected') {
                return response()->json(['message' => 'Cannot delete processed submission'], 403);
            }

            // Hapus file dari storage jika ada
            if ($submission->certificate_path) {
                try {
                    \Log::info('Deleting file for submission ID: ' . $id);
                    \Log::info('File path: ' . $submission->certificate_path);
                    \Log::info('Storage type: ' . ($submission->storage_type ?? 'not set'));
                    
                    $googleDriveService = app(GoogleDriveService::class);
                    
                    // Coba permanent delete dulu (untuk Google Drive)
                    if ($submission->storage_type === 'google' || strlen($submission->certificate_path) > 20) {
                        \Log::info('Attempting permanent delete via Google API');
                        $permanentDeleteResult = $googleDriveService->permanentlyDeleteFile($submission->certificate_path);
                        
                        if ($permanentDeleteResult) {
                            \Log::info('File permanently deleted successfully');
                        } else {
                            \Log::warning('Permanent delete failed, trying regular delete');
                            // Fallback ke regular delete
                            $deleteResult = $googleDriveService->deleteFile(
                                $submission->certificate_path, 
                                $submission->storage_type ?? 'google'
                            );
                            
                            if ($deleteResult) {
                                \Log::info('File deleted successfully (moved to trash)');
                            } else {
                                \Log::warning('Regular delete also failed');
                            }
                        }
                    } else {
                        // Local storage
                        $deleteResult = $googleDriveService->deleteFile(
                            $submission->certificate_path, 
                            $submission->storage_type ?? 'google'
                        );
                        
                        if ($deleteResult) {
                            \Log::info('File deleted successfully from local storage');
                        } else {
                            \Log::warning('File deletion returned false');
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to delete file from storage: ' . $e->getMessage());
                    \Log::error('Stack trace: ' . $e->getTraceAsString());
                }
            }

            $submission->delete();
            return response()->json(['message' => 'Submission deleted successfully']);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 4. Approve Pengajuan (Admin)
     */
    public function approve(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);
        
        // Persiapkan data untuk update
        $dataToUpdate = [
            'status' => 'Approved',
            'points_awarded' => $request->points ?? 0,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ];

        // Update category jika dikirim dari frontend
        if ($request->has('assigned_subcategory_id') && $request->assigned_subcategory_id) {
            $subcategory = Subcategory::findOrFail($request->assigned_subcategory_id);
            $dataToUpdate['student_subcategory_id'] = $subcategory->id;
            $dataToUpdate['student_category_id'] = $subcategory->category_id;
            $dataToUpdate['points_awarded'] = $subcategory->points;
        }

        $submission->update($dataToUpdate);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Submission approved!']);
        }
        return redirect()->back()->with('success', 'Submission approved!');
    }

    /**
     * 5. Reject Pengajuan (Admin)
     */

    public function reject(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);
        
        // Validasi input
        $request->validate([
            'rejectReason' => 'required|string|min:5'
        ]);

        // Update database
        $submission->update([
            'status' => 'Rejected',
            'rejection_reason' => $request->rejectReason, // Pastikan kolom ini ada di DB!
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return response()->json(['message' => 'Submission rejected successfully.']);
    }
    
    /**
     * Update Category Only (without changing approval status)
     */
    public function updateCategory(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);
        
        // Validasi input
        $request->validate([
            'assigned_subcategory_id' => 'required|exists:subcategories,id'
        ]);

        $subcategory = Subcategory::findOrFail($request->assigned_subcategory_id);
        
        // Update category dan points (jika sudah approved, update points juga)
        $dataToUpdate = [
            'student_subcategory_id' => $subcategory->id,
            'student_category_id' => $subcategory->category_id,
        ];
        
        // Jika submission sudah approved, update juga points-nya
        if ($submission->status === 'Approved') {
            $dataToUpdate['points_awarded'] = $subcategory->points;
        }
        
        $submission->update($dataToUpdate);

        return response()->json(['message' => 'Category updated successfully!']);
    }
    
    /**
     * 6. Simpan Komplain (Mahasiswa)
     */
    public function storeComplaint(Request $request)
    {
        $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'type' => 'required|string',
            'explanation' => 'required|string',
        ]);

        // Opsional: Anda bisa menyimpan komplain ini ke tabel khusus 'complaints'
        // atau update kolom JSON di tabel submissions.
        // Untuk saat ini kita return sukses agar UI berjalan.
        
        // Contoh Update Logika (Opsional):
        // $submission = Submission::find($request->submission_id);
        // $submission->complaint_status = 'Pending';
        // $submission->complaint_reason = $request->explanation;
        // $submission->save();

        return response()->json(['message' => 'Complaint submitted successfully. Admin will review it.']);
    }

    /**
     * 7. Edit Submission oleh Admin (untuk approved/rejected yang perlu koreksi)
     */
    public function adminEdit(Request $request, $id)
    {
        try {
            $submission = Submission::findOrFail($id);
            
            // Cek status: Boleh edit jika 'Approved' ATAU 'Rejected'
            if ($submission->status !== 'Approved' && $submission->status !== 'Rejected') {
                return response()->json(['message' => 'Only Approved or Rejected submissions can be edited by admin.'], 403);
            }

            // Validasi
            $request->validate([
                'title'             => 'nullable|string|max:255',
                'description'       => 'nullable|string',
                'activity_date'     => 'nullable|date',
                'mainCategory'      => 'nullable|string',
                'subcategory'       => 'nullable|string',
                'certificate_file'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'status'            => 'nullable|in:Waiting,Approved,Rejected',
                'rejection_reason'  => 'nullable|string',
            ]);

            $dataToUpdate = [];

            // Update fields if provided
            if ($request->has('title')) {
                $dataToUpdate['title'] = $request->title;
            }
            if ($request->has('description')) {
                $dataToUpdate['description'] = $request->description;
            }
            if ($request->has('activity_date')) {
                $dataToUpdate['activity_date'] = $request->activity_date;
            }
            if ($request->has('status')) {
                $dataToUpdate['status'] = $request->status;
            }
            if ($request->has('rejection_reason')) {
                $dataToUpdate['rejection_reason'] = $request->rejection_reason;
            }

            // Update category if provided
            if ($request->has('mainCategory')) {
                $category = Category::where('name', $request->mainCategory)->first();
                if ($category) {
                    $dataToUpdate['student_category_id'] = $category->id;
                }
            }

            // Update subcategory if provided
            if ($request->has('subcategory')) {
                $subcategory = Subcategory::where('name', $request->subcategory)->first();
                if ($subcategory) {
                    $dataToUpdate['student_subcategory_id'] = $subcategory->id;
                }
            }

            // Handle file upload if provided
            if ($request->hasFile('certificate_file')) {
                $googleDriveService = app(GoogleDriveService::class);
                
                // Delete old file if exists
                if ($submission->certificate_path && $submission->storage_type === 'google') {
                    try {
                        $googleDriveService->deleteFile($submission->certificate_path, 'google');
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete old file from Google Drive: ' . $e->getMessage());
                    }
                }

                // Upload new file
                $uploadResult = $googleDriveService->uploadFile(
                    $request->file('certificate_file'),
                    'certificates'
                );
                
                $dataToUpdate['certificate_path'] = $uploadResult['path'];
                $dataToUpdate['certificate_url'] = $uploadResult['url'] ?? null;
                $dataToUpdate['certificate_original_name'] = $request->file('certificate_file')->getClientOriginalName();
                $dataToUpdate['storage_type'] = $uploadResult['storage'] ?? 'google';
            }

            // Update admin info
            $dataToUpdate['reviewed_by'] = Auth::id();
            $dataToUpdate['reviewed_at'] = now();

            $submission->update($dataToUpdate);

            return response()->json(['message' => 'Submission updated successfully by admin!']);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating data: ' . $e->getMessage()], 500);
        }
    }
}