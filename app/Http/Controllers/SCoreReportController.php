<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Submission;
use App\Helpers\SCoreHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SCoreReportController extends Controller
{
    /**
     * Download S-Core Report (Admin or Student themselves)
     */
    public function downloadReport($student_id)
    {
        $student = User::where('student_id', $student_id)->firstOrFail();
        
        // Authorization: hanya admin atau student sendiri yang bisa download
        if (Auth::user()->role !== 'admin' && Auth::user()->student_id !== $student_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check eligibility
        $eligibility = SCoreHelper::checkSCoreEligibility($student->id);
        
        // Allow admin to bypass eligibility check, but reject for students who don't meet requirements
        if (!$eligibility['isEligible'] && Auth::user()->role !== 'admin') {
            return response()->json([
                'message' => 'Student does not meet S-Core requirements',
                'details' => [
                    'points' => $eligibility['totalPoints'],
                    'pointsMet' => $eligibility['minPointsMet'],
                    'categories' => $eligibility['completedCategories'],
                    'categoriesMet' => $eligibility['minCategoriesMet']
                ]
            ], 422);
        }

        // Get student data and category breakdown
        $categoryBreakdown = SCoreHelper::getCategoryBreakdown($student->id);
        
        $data = [
            'student' => $student,
            'totalPoints' => $eligibility['totalPoints'],
            'completedCategories' => $eligibility['completedCategories'],
            'totalCategories' => $eligibility['totalCategories'],
            'categoryBreakdown' => $categoryBreakdown,
            'generatedDate' => now()->format('d M Y'),
            'generatedTime' => now()->format('H:i')
        ];

        // Generate PDF
        $pdf = Pdf::loadView('reports.score-report', $data);
        
        // Download file
        return $pdf->download('S-Core-Report-' . $student->student_id . '-' . now()->format('YmdHis') . '.pdf');
    }

    /**
     * Check S-Core eligibility (for API calls)
     */
    public function checkEligibility($student_id)
    {
        $student = User::where('student_id', $student_id)->firstOrFail();
        
        // Authorization
        if (Auth::user()->role !== 'admin' && Auth::user()->student_id !== $student_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $eligibility = SCoreHelper::checkSCoreEligibility($student->id);
        
        return response()->json([
            'totalPoints' => $eligibility['totalPoints'],
            'minPointsMet' => $eligibility['minPointsMet'],
            'completedCategories' => $eligibility['completedCategories'],
            'totalCategories' => $eligibility['totalCategories'],
            'minCategoriesMet' => $eligibility['minCategoriesMet'],
            'isEligible' => $eligibility['isEligible']
        ]);
    }

    /**
     * Get S-Core status for display
     */
    public function getStatus($student_id)
    {
        $student = User::where('student_id', $student_id)->firstOrFail();
        $eligibility = SCoreHelper::checkSCoreEligibility($student->id);
        $categoryBreakdown = SCoreHelper::getCategoryBreakdown($student->id);
        
        return response()->json([
            'totalPoints' => $eligibility['totalPoints'],
            'completedCategories' => $eligibility['completedCategories'],
            'categoryBreakdown' => $categoryBreakdown,
            'isEligible' => $eligibility['isEligible'],
            'minPointsMet' => $eligibility['minPointsMet'],
            'minCategoriesMet' => $eligibility['minCategoriesMet']
        ]);
    }
}
