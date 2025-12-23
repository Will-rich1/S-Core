<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // --- MAIN CATEGORY ---
    
    // 1. Simpan Main Category Baru (Gunakan yang VERSI BARU ini)
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        // Auto-increment display_order
        $maxOrder = Category::max('display_order') ?? 0;

        $category = Category::create([
            'name' => $request->name,
            'is_active' => true,
            'display_order' => $maxOrder + 1
        ]);

        // PENTING: Siapkan relasi subcategories kosong agar struktur JSON konsisten
        // Ini biar di javascript tidak error saat membaca .subcategories
        $category->subcategories = []; 

        // Return object category yang baru dibuat ke frontend
        return response()->json([
            'message' => 'Main Category added successfully!',
            'category' => $category 
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category = Category::findOrFail($id);
        $category->update(['name' => $request->name]);

        return response()->json(['message' => 'Category updated successfully!']);
    }

    // DELETE MAIN CATEGORY
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Validasi: Jangan hapus jika sudah dipakai di Submission
        if ($category->submissions()->count() > 0) {
            return response()->json(['message' => 'Gagal hapus: Kategori ini sudah dipakai di data S-Core mahasiswa.'], 403);
        }

        // Hapus subcategories anaknya dulu
        $category->subcategories()->delete();
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully!']);
    }

    // --- SUBCATEGORY ---

    public function storeSubcategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'points' => 'required|integer|min:0',
            'description' => 'nullable|string'
        ]);

        Subcategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'points' => $request->points,
            'description' => $request->description,
            'is_active' => true
        ]);

        return response()->json(['message' => 'Subcategory added successfully!']);
    }

    public function updateSubcategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'points' => 'required|integer|min:0',
            'description' => 'nullable|string'
        ]);

        $sub = Subcategory::findOrFail($id);
        $sub->update([
            'name' => $request->name,
            'points' => $request->points,
            'description' => $request->description
        ]);

        return response()->json(['message' => 'Subcategory updated successfully!']);
    }

    public function destroySubcategory($id)
    {
        $sub = Subcategory::findOrFail($id);
        
        if ($sub->submissions()->count() > 0) {
            return response()->json(['message' => 'Cannot delete subcategory because it is used in submissions.'], 403);
        }

        $sub->delete();
        return response()->json(['message' => 'Subcategory deleted successfully!']);
    }
}