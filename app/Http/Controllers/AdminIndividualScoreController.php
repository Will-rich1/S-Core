<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminIndividualScoreController extends Controller
{
    /**
     * Create an admin-assigned individual S-Core submission for a single student.
     */
    public function store(Request $request, string $studentId)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }

        $student = User::query()
            ->where('role', 'student')
            ->where('student_id', $studentId)
            ->firstOrFail();

        $validated = $request->validate([
            'subcategory_id' => 'required|integer|exists:subcategories,id',
            'activityTitle' => 'required|string|max:500',
            'description' => 'required|string',
            'activityDate' => 'required|date',
        ]);

        $subcategory = Subcategory::query()
            ->with('category:id,name')
            ->where('id', $validated['subcategory_id'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereRaw('LOWER(subcategories.name) LIKE ?', ['%orkess%'])
                    ->orWhereRaw('LOWER(subcategories.name) LIKE ?', ['%retreat%'])
                    ->orWhereHas('category', function ($categoryQuery) {
                        $categoryQuery->whereRaw('LOWER(name) LIKE ?', ['%orkess%'])
                            ->orWhereRaw('LOWER(name) LIKE ?', ['%retreat%']);
                    });
            })
            ->first();

        if (!$subcategory || !$subcategory->category) {
            return redirect()
                ->route('admin.students.detail', ['studentId' => $student->student_id])
                ->with('error', 'Subkategori individu tidak valid. Pilih OrKeSS atau Retreat yang aktif.');
        }

        Submission::create([
            'student_id' => $student->id,
            'student_category_id' => $subcategory->category->id,
            'student_subcategory_id' => $subcategory->id,
            'assigned_category_id' => $subcategory->category->id,
            'assigned_subcategory_id' => $subcategory->id,
            'title' => $validated['activityTitle'],
            'description' => $validated['description'],
            'activity_date' => $validated['activityDate'],
            'semester_cycle' => max(0, (int) ($student->semester_offset ?? 0)),
            'status' => 'Approved',
            'points_awarded' => $subcategory->points,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('admin.students.detail', ['studentId' => $student->student_id])
            ->with('success', 'Penugasan S-Core individu berhasil ditambahkan.');
    }
}
