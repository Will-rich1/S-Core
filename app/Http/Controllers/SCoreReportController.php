<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\SCoreHelper;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SCoreReportController extends Controller
{
    private function buildReportData($student_id)
    {
        $student = User::where('student_id', $student_id)->firstOrFail();

        // Authorization: hanya admin atau student sendiri yang bisa akses report
        if (Auth::user()->role !== 'admin' && Auth::user()->student_id !== $student_id) {
            abort(403, 'Unauthorized');
        }

        // Allow admin to bypass eligibility check, but reject for students who don't meet requirements
        $eligibility = SCoreHelper::checkSCoreEligibility($student->id);
        if (!$eligibility['isEligible'] && Auth::user()->role !== 'admin') {
            abort(422, 'Student does not meet S-Core requirements');
        }

        $categoryBreakdown = SCoreHelper::getCategoryBreakdown($student->id);

        return [
            'student' => $student,
            'totalPoints' => $eligibility['totalPoints'],
            'completedCategories' => $eligibility['completedCategories'],
            'totalCategories' => $eligibility['totalCategories'],
            'minPointsRequired' => $eligibility['minPointsRequired'],
            'minCategoriesRequired' => $eligibility['minCategoriesRequired'],
            'isPassed' => $eligibility['isEligible'],
            'categoryBreakdown' => $categoryBreakdown,
            'generatedDate' => now()->format('d M Y'),
            'generatedTime' => now()->format('H:i')
        ];
    }

    /**
     * View S-Core Report in browser tab (inline PDF)
     */
    public function viewReport($student_id)
    {
        $data = $this->buildReportData($student_id);

        $pdf = Pdf::loadView('reports.score-report', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('S-Core-Report-' . $data['student']->student_id . '.pdf');
    }

    /**
     * Download S-Core Report (Admin or Student themselves)
     */
    public function downloadReport($student_id)
    {
        $data = $this->buildReportData($student_id);

        // Generate PDF with proper settings
        $pdf = Pdf::loadView('reports.score-report', $data)
            ->setPaper('a4', 'portrait');
        
        // Download file
        return $pdf->download('S-Core-Report-' . $data['student']->student_id . '-' . now()->format('YmdHis') . '.pdf');
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
            'isEligible' => $eligibility['isEligible'],
            'minPointsRequired' => $eligibility['minPointsRequired'],
            'minCategoriesRequired' => $eligibility['minCategoriesRequired']
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
            'totalCategories' => $eligibility['totalCategories'],
            'categoryBreakdown' => $categoryBreakdown,
            'isEligible' => $eligibility['isEligible'],
            'minPointsMet' => $eligibility['minPointsMet'],
            'minCategoriesMet' => $eligibility['minCategoriesMet'],
            'minPointsRequired' => $eligibility['minPointsRequired'],
            'minCategoriesRequired' => $eligibility['minCategoriesRequired']
        ]);
    }
}
