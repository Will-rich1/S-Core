<?php

namespace App\Helpers;

use App\Models\Submission;
use App\Models\Category;

class SCoreHelper
{
    // Total minimum categories required (5 dari 6)
    const MIN_CATEGORIES_REQUIRED = 5;
    const MIN_POINTS_REQUIRED = 20;

    /**
     * Check if student meets S-Core requirements
     * - Minimum 20 points (approved submissions)
     * - Minimum 5 dari 6 main categories
     */
    public static function checkSCoreEligibility($studentId)
    {
        // Get total approved points
        $totalPoints = Submission::where('student_id', $studentId)
            ->where('status', 'Approved')
            ->sum('points_awarded');

        // Get unique main categories with at least 1 approved submission
        $completedCategories = Submission::where('student_id', $studentId)
            ->where('status', 'Approved')
            ->distinct('student_category_id')
            ->count('student_category_id');

        // Get total number of main categories (should be 6 total)
        $totalCategories = Category::where('is_active', true)->count();

        return [
            'totalPoints' => $totalPoints,
            'minPointsMet' => $totalPoints > self::MIN_POINTS_REQUIRED,
            'completedCategories' => $completedCategories,
            'totalCategories' => $totalCategories,
            'minCategoriesMet' => $completedCategories >= self::MIN_CATEGORIES_REQUIRED,
            'isEligible' => ($totalPoints > self::MIN_POINTS_REQUIRED) && ($completedCategories >= self::MIN_CATEGORIES_REQUIRED)
        ];
    }

    /**
     * Get category breakdown for a student
     */
    public static function getCategoryBreakdown($studentId)
    {
        $breakdown = Submission::where('student_id', $studentId)
            ->where('status', 'Approved')
            ->with(['category', 'subcategory'])
            ->get()
            ->groupBy('student_category_id')
            ->map(function ($submissions, $categoryId) {
                $category = $submissions->first()->category;
                return [
                    'categoryId' => $categoryId,
                    'categoryName' => $category->name ?? 'Unknown',
                    'count' => $submissions->count(),
                    'points' => $submissions->sum('points_awarded'),
                    'submissions' => $submissions->map(function ($sub) {
                        return [
                            'id' => $sub->id,
                            'title' => $sub->title,
                            'subcategory' => $sub->subcategory->name ?? 'Unknown',
                            'points' => $sub->points_awarded,
                            'date' => $sub->activity_date
                        ];
                    })->toArray()
                ];
            })
            ->sortBy('categoryName')
            ->values(); // Reset array keys

        return $breakdown;
    }

    /**
     * Get eligible categories for report generation
     */
    public static function getEligibleCategories($studentId)
    {
        $categories = Submission::where('student_id', $studentId)
            ->where('status', 'Approved')
            ->with('category')
            ->distinct('student_category_id')
            ->pluck('category.name', 'student_category_id')
            ->toArray();

        return $categories;
    }
}
