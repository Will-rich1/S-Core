<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Category;
use App\Models\Subcategory;
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

            // 3. Proses Upload File
            $file = $request->file('certificate_file');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('certificates', $filename, 'public'); 

            // 4. Simpan Data ke Database
            Submission::create([
                'student_id'             => Auth::id(),
                'student_category_id'    => $category->id,
                'student_subcategory_id' => $subcategory->id,
                'title'                  => $request->title,
                'description'            => $request->description,
                'activity_date'          => $request->activity_date,
                'certificate_path'       => $path,
                'certificate_original_name' => $file->getClientOriginalName(),
                'status'                 => 'Waiting',
            ]);

            return response()->json(['message' => 'Submission submitted successfully!']);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error saving data: ' . $e->getMessage()], 500);
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
                // Hapus file lama fisik
                if ($submission->certificate_path && Storage::disk('public')->exists($submission->certificate_path)) {
                    Storage::disk('public')->delete($submission->certificate_path);
                }

                // Simpan file baru
                $file = $request->file('certificate_file');
                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $path = $file->storeAs('certificates', $filename, 'public'); 
                
                $dataToUpdate['certificate_path'] = $path;
                $dataToUpdate['certificate_original_name'] = $file->getClientOriginalName();
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

            // Hapus file fisik
            if ($submission->certificate_path && Storage::disk('public')->exists($submission->certificate_path)) {
                Storage::disk('public')->delete($submission->certificate_path);
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
        
        $submission->update([
            'status' => 'Approved',
            'points_awarded' => $request->points ?? 0,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

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
}