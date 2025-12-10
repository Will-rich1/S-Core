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
     * Diakses via FETCH API dari Dashboard
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
            // (Karena dropdown di frontend mengirim nama string, bukan ID)
            $category = Category::where('name', $request->mainCategory)->first();
            
            if (!$category) {
                return response()->json(['message' => 'Category not found. Please select a valid category.'], 422);
            }

            $subcategory = Subcategory::where('category_id', $category->id)
                                      ->where('name', $request->subcategory)
                                      ->first();

            if (!$subcategory) {
                // Fallback: Coba cari subkategori berdasarkan nama saja jika query relasi gagal
                $subcategory = Subcategory::where('name', $request->subcategory)->first();
                if (!$subcategory) {
                    return response()->json(['message' => 'Subcategory not found.'], 422);
                }
            }

            // 3. Proses Upload File
            // Simpan ke folder 'storage/app/public/certificates'
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
                'status'                 => 'Waiting', // Default status menunggu review
            ]);

            // 5. Kirim Respon Sukses (JSON) untuk Javascript
            return response()->json(['message' => 'Submission submitted successfully!']);

        } catch (\Exception $e) {
            // Jika ada error server
            return response()->json(['message' => 'Error saving data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 2. Update Pengajuan (Jika Mahasiswa mengedit data Waiting)
     */
    public function update(Request $request, $id)
    {
        // Fitur edit bisa ditambahkan nanti, prinsipnya mirip store
        // tapi pakai $submission->update(...)
        return response()->json(['message' => 'Update feature coming soon']);
    }

    /**
     * 3. Hapus Pengajuan (Mahasiswa)
     */
    public function destroy($id)
    {
        $submission = Submission::where('student_id', Auth::id())->where('id', $id)->first();
        
        if ($submission && $submission->status === 'Waiting') {
            // Hapus file fisik
            if ($submission->certificate_path) {
                Storage::disk('public')->delete($submission->certificate_path);
            }
            $submission->delete();
            return response()->json(['message' => 'Submission deleted successfully']);
        }

        return response()->json(['message' => 'Cannot delete this submission'], 403);
    }

    /**
     * 4. Approve Pengajuan (Admin)
     * Diakses via Fetch/Form Admin
     */
    public function approve(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);
        
        // Cari subkategori yang ditentukan admin (bisa jadi admin mengubah kategori)
        // Kita cari berdasarkan Nama yang dikirim admin, atau ID jika admin mengirim ID
        // Asumsi Admin mengirim Nama kategori lewat Javascript:
        
        // Logic Simple: Jika admin tidak kirim data baru, pakai data lama
        $submission->update([
            'status' => 'Approved',
            'points_awarded' => $request->points ?? 0, // Poin dari input admin
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Jika request ini AJAX (dari fetch JS)
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Submission approved!']);
        }
        // Jika request Form biasa
        return redirect()->back()->with('success', 'Submission approved!');
    }

    /**
     * 5. Reject Pengajuan (Admin)
     */
    public function reject(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);
        
        $submission->update([
            'status' => 'Rejected',
            'rejection_reason' => $request->rejectReason ?? 'No reason provided',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Submission rejected.']);
        }
        return redirect()->back()->with('success', 'Submission rejected.');
    }
    
    // Simpan Komplain (Mahasiswa)
    public function storeComplaint(Request $request)
    {
        // Validasi dan simpan ke tabel 'complaints'
        // ... (Implementasi nanti)
        return response()->json(['message' => 'Complaint submitted']);
    }
}