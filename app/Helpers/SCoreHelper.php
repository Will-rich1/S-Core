<?php

namespace App\Helpers;

use App\Models\Submission;
use App\Models\Category;
use App\Models\SystemSetting;
use App\Models\User;

class SCoreHelper
{
    // Fallback constants if settings not found
    const DEFAULT_MIN_CATEGORIES_REQUIRED = 5;
    const DEFAULT_MIN_POINTS_REQUIRED = 20;
    const YEAR_2022_MIN_CATEGORIES_REQUIRED = 4;
    const YEAR_2022_TOTAL_CATEGORIES = 5;

    /**
     * Get minimum points required from settings
     */
    public static function getMinPointsRequired()
    {
        return SystemSetting::getSetting('min_points_required', self::DEFAULT_MIN_POINTS_REQUIRED);
    }

    /**
     * Get minimum categories required from settings
     */
    public static function getMinCategoriesRequired($studentId = null)
    {
        $defaultMinCategories = SystemSetting::getSetting('min_categories_required', self::DEFAULT_MIN_CATEGORIES_REQUIRED);

        if (!$studentId) {
            return $defaultMinCategories;
        }

        $student = User::find($studentId);
        if (!$student) {
            return $defaultMinCategories;
        }

        return self::getMinCategoriesRequiredForYear($student->year, $student->student_id, $defaultMinCategories);
    }

    /**
     * Get minimum categories required based on student year rule.
     * 2022 students: minimum 4 categories.
     */
    public static function getMinCategoriesRequiredForYear($year = null, $studentPublicId = null, $defaultMinCategories = null)
    {
        $defaultValue = $defaultMinCategories ?? SystemSetting::getSetting('min_categories_required', self::DEFAULT_MIN_CATEGORIES_REQUIRED);
        $entryYear = self::resolveEntryYear($year, $studentPublicId);

        if ($entryYear === 2022) {
            return self::YEAR_2022_MIN_CATEGORIES_REQUIRED;
        }

        return $defaultValue;
    }

    /**
     * Get total categories target shown to the student.
     * 2022 students: 5 categories.
     */
    public static function getTotalCategoriesForStudent($studentId)
    {
        $defaultTotalCategories = Category::where('is_active', true)->count();
        $student = User::find($studentId);

        if (!$student) {
            return $defaultTotalCategories;
        }

        $entryYear = self::resolveEntryYear($student->year, $student->student_id);
        if ($entryYear === 2022) {
            return self::YEAR_2022_TOTAL_CATEGORIES;
        }

        return $defaultTotalCategories;
    }

    private static function resolveEntryYear($year = null, $studentPublicId = null)
    {
        if (!empty($year)) {
            $yearDigits = preg_replace('/\D/', '', (string) $year);
            if (strlen($yearDigits) === 4) {
                return (int) $yearDigits;
            }
            if (strlen($yearDigits) === 2) {
                return 2000 + (int) $yearDigits;
            }
        }

        if (!empty($studentPublicId)) {
            $studentDigits = preg_replace('/\D/', '', (string) $studentPublicId);
            if (strlen($studentDigits) >= 2) {
                return 2000 + (int) substr($studentDigits, 0, 2);
            }
        }

        return null;
    }

    /**
     * Check if student meets S-Core requirements
     * - Minimum points (configurable, default 20)
     * - Minimum categories (configurable, default 5 dari 6)
     */
    public static function checkSCoreEligibility($studentId)
    {
        $minPoints = self::getMinPointsRequired();
        $minCategories = self::getMinCategoriesRequired($studentId);

        // Get total approved points
        $totalPoints = Submission::where('student_id', $studentId)
            ->where('status', 'Approved')
            ->sum('points_awarded');

        // Get unique main categories with at least 1 approved submission
        $completedCategories = Submission::where('student_id', $studentId)
            ->where('status', 'Approved')
            ->distinct('student_category_id')
            ->count('student_category_id');

        $totalCategories = self::getTotalCategoriesForStudent($studentId);

        return [
            'totalPoints' => $totalPoints,
            'minPointsMet' => $totalPoints >= $minPoints,
            'completedCategories' => $completedCategories,
            'totalCategories' => $totalCategories,
            'minCategoriesMet' => $completedCategories >= $minCategories,
            'isEligible' => ($totalPoints >= $minPoints) && ($completedCategories >= $minCategories),
            'minPointsRequired' => $minPoints,
            'minCategoriesRequired' => $minCategories
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
