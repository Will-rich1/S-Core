<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CategoryController extends Controller
{
    // --- GET ALL CATEGORIES (API) ---
    // Endpoint ini digunakan oleh frontend untuk fetch kategori secara real-time
    public function index()
    {
        $includeInactive = request()->query('include_inactive') == '1';

        $query = Category::with(['subcategories' => function($query) use ($includeInactive) {
            if (!$includeInactive) {
                $query->where('is_active', true);
            }
            // If includeInactive is true, get ALL subcategories (no filter)
        }])->orderBy('display_order');
        
        if (!$includeInactive) {
            $query->where('is_active', true);
        }

        $categories = $query->get();

        return response()->json($categories);
    }

    // --- GET CATEGORIES FOR STUDENT (API) ---
    // Special endpoint untuk student dashboard dengan calculation submission count
    public function studentCategories()
    {
        $currentSemesterCycle = max(0, (int) (Auth::user()->semester_offset ?? 0));

        $currentSemesterCounts = Submission::query()
            ->select('student_category_id', DB::raw('COUNT(*) as total'))
            ->where('student_id', Auth::id())
            ->where('semester_cycle', $currentSemesterCycle)
            ->whereIn('status', ['Waiting', 'Approved'])
            ->groupBy('student_category_id')
            ->pluck('total', 'student_category_id');

        $rawCategories = Category::with('subcategories')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        // Only include active subcategories for students
        $categoryGroups = $rawCategories->map(function($cat) use ($currentSemesterCounts) {
            $maxPerSemester = $cat->max_submissions_per_semester !== null
                ? (int) $cat->max_submissions_per_semester
                : null;
            $usedThisSemester = (int) ($currentSemesterCounts[$cat->id] ?? 0);
            $isQuotaFull = $maxPerSemester !== null && $usedThisSemester >= $maxPerSemester;

            $activeSubs = $cat->subcategories->filter(function ($sub) {
                return ($sub->is_active == 1 || $sub->is_active === true || $sub->is_active === '1');
            })->values();

            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'max_submissions_per_semester' => $maxPerSemester,
                'used_this_semester' => $usedThisSemester,
                'is_quota_full' => $isQuotaFull,
                'subcategories' => $activeSubs->map(function($sub) {
                    return [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'points' => $sub->points,
                        'description' => $sub->description,
                        'approvedCount' => \App\Models\Submission::where('student_id', Auth::id())
                                                    ->where('student_subcategory_id', $sub->id)
                                                    ->where('status', 'Approved')
                                                    ->count()
                    ];
                })
            ];
        })->values();

        return response()->json($categoryGroups);
    }

    // Reactivate main category
    public function reactivate($id)
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => true]);

        // Log activity (best-effort, do not break the request if logging fails)
            try {
                $this->writeActivityLog('reactivate_category', 'category', $category->id, 'Reactivated category: '.$category->name);
            } catch (\Exception $e) {
                Log::error('Failed to insert activity_log for reactivate category: '.$e->getMessage());
            }

        return response()->json(['message' => 'Category reactivated successfully']);
    }

    // Reactivate subcategory
    public function reactivateSubcategory($id)
    {
        $sub = Subcategory::findOrFail($id);
        $sub->update(['is_active' => true]);

        try {
            $this->writeActivityLog('reactivate_subcategory', 'subcategory', $sub->id, 'Reactivated subcategory: '.$sub->name);
        } catch (\Exception $e) {
            Log::error('Failed to insert activity_log for reactivate subcategory: '.$e->getMessage());
        }

        return response()->json(['message' => 'Subcategory reactivated successfully']);
    }

    // --- MAIN CATEGORY ---
    
    // 1. Simpan Main Category Baru (Gunakan yang VERSI BARU ini)
    public function store(Request $request)
    {
        $maxPerSemester = $this->normalizeMaxSubmissionsPerSemester($request->input('max_submissions_per_semester'));

        $request->merge(['max_submissions_per_semester' => $maxPerSemester]);
        $request->validate([
            'name' => 'required|string|max:255',
            'max_submissions_per_semester' => 'nullable|integer|min:1|max:10'
        ]);
        
        // Auto-increment display_order
        $maxOrder = Category::max('display_order') ?? 0;

        $category = Category::create([
            'name' => $request->name,
            'is_active' => true,
            'display_order' => $maxOrder + 1,
            'max_submissions_per_semester' => $request->max_submissions_per_semester,
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
        $maxPerSemester = $this->normalizeMaxSubmissionsPerSemester($request->input('max_submissions_per_semester'));

        $request->merge(['max_submissions_per_semester' => $maxPerSemester]);
        $request->validate([
            'name' => 'required|string|max:255',
            'max_submissions_per_semester' => 'nullable|integer|min:1|max:10'
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'max_submissions_per_semester' => $request->max_submissions_per_semester,
        ]);

        return response()->json(['message' => 'Category updated successfully!']);
    }

    // DELETE MAIN CATEGORY (Soft Delete Only)
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Selalu soft delete dengan set is_active = false
        // Tidak pernah melakukan hard delete permanent
        $category->update(['is_active' => false]);
        
        // Juga nonaktifkan semua subcategories
        $category->subcategories()->update(['is_active' => false]);
        
        // Log deactivation (best-effort)
        try {
            $this->writeActivityLog('deactivate_category', 'category', $category->id, 'Deactivated category: '.$category->name);
        } catch (\Exception $e) {
            Log::error('Failed to insert activity_log for deactivate category: '.$e->getMessage());
        }

        return response()->json(['message' => 'Category deactivated successfully!']);
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
        
        // Selalu soft delete dengan set is_active = false
        // Tidak pernah melakukan hard delete permanent
        $sub->update(['is_active' => false]);
        
        try {
            $this->writeActivityLog('deactivate_subcategory', 'subcategory', $sub->id, 'Deactivated subcategory: '.$sub->name);
        } catch (\Exception $e) {
            Log::error('Failed to insert activity_log for deactivate subcategory: '.$e->getMessage());
        }

        return response()->json(['message' => 'Subcategory deactivated successfully!']);
    }

    // Helper: write activity log supporting old/new schema
    private function writeActivityLog(string $action, string $entityType = null, $entityId = null, string $message = null)
    {
        $now = now();

        if (Schema::hasColumn('activity_logs', 'entity_type')) {
            DB::table('activity_logs')->insert([
                'user_id' => Auth::id(),
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'details' => json_encode(['message' => $message]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => $now,
                'updated_at' => $now
            ]);
            return;
        }

        // Fallback for older schema naming (target_type/target_id/message)
        if (Schema::hasColumn('activity_logs', 'target_type')) {
            DB::table('activity_logs')->insert([
                'user_id' => Auth::id(),
                'action' => $action,
                'target_type' => $entityType,
                'target_id' => $entityId,
                'message' => $message,
                'created_at' => $now,
                'updated_at' => $now
            ]);
            return;
        }

        // Last resort: insert minimal fields if schema is different
        DB::table('activity_logs')->insert([
            'user_id' => Auth::id(),
            'action' => $action,
            'details' => json_encode(['message' => $message]),
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }

    private function normalizeMaxSubmissionsPerSemester($value): ?int
    {
        if ($value === null || $value === '' || $value === 'none') {
            return null;
        }

        return (int) $value;
    }
}