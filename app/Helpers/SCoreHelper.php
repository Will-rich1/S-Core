<?php

namespace App\Helpers;

use App\Models\Submission;
use App\Models\Category;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;

class SCoreHelper
{
    // Fallback constants if settings not found
    const DEFAULT_MIN_CATEGORIES_REQUIRED = 5;
    const DEFAULT_MIN_POINTS_REQUIRED = 20;
    const DEFAULT_PERFECT_MIN_POINTS = 40;
    const DEFAULT_SUBMISSION_DATE_RULE_MODE = 'rolling_days';
    const DEFAULT_SUBMISSION_DATE_RANGE_DAYS = 30;
    const DEFAULT_STUDENT_MAINTENANCE_MODE = false;
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
     * Get minimum points required for Perfect criteria
     */
    public static function getPerfectMinPointsRequired()
    {
        return SystemSetting::getSetting('perfect_min_points', self::DEFAULT_PERFECT_MIN_POINTS);
    }

    /**
     * Get submission date rule mode.
     * Available: rolling_days | fixed_start_date
     */
    public static function getSubmissionDateRuleMode(): string
    {
        $mode = (string) SystemSetting::getSetting('submission_date_rule_mode', self::DEFAULT_SUBMISSION_DATE_RULE_MODE);

        return in_array($mode, ['rolling_days', 'fixed_start_date'], true)
            ? $mode
            : self::DEFAULT_SUBMISSION_DATE_RULE_MODE;
    }

    /**
     * Get rolling submission date range in days.
     */
    public static function getSubmissionDateRangeDays(): int
    {
        $days = (int) SystemSetting::getSetting('submission_date_range_days', self::DEFAULT_SUBMISSION_DATE_RANGE_DAYS);

        return max(1, $days);
    }

    /**
     * Get fixed submission start date (Y-m-d) or null.
     */
    public static function getSubmissionStartDate(): ?string
    {
        $startDate = SystemSetting::getSetting('submission_start_date', null);
        if (empty($startDate)) {
            return null;
        }

        try {
            return Carbon::parse($startDate)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Whether student area is currently in maintenance mode.
     */
    public static function isStudentMaintenanceModeEnabled(): bool
    {
        return (bool) SystemSetting::getSetting('student_maintenance_mode', self::DEFAULT_STUDENT_MAINTENANCE_MODE);
    }

    /**
     * Validate activity date against system submission date rules.
     */
    public static function validateSubmissionActivityDate(string $activityDate): array
    {
        try {
            $activity = Carbon::parse($activityDate)->startOfDay();
        } catch (\Throwable $e) {
            return [
                'valid' => false,
                'message' => 'Tanggal kegiatan tidak valid.',
            ];
        }

        $today = now()->startOfDay();
        if ($activity->greaterThan($today)) {
            return [
                'valid' => false,
                'message' => 'Tanggal kegiatan tidak boleh di masa depan.',
            ];
        }

        $mode = self::getSubmissionDateRuleMode();
        if ($mode === 'fixed_start_date') {
            $startDate = self::getSubmissionStartDate();
            if (!empty($startDate)) {
                $start = Carbon::parse($startDate)->startOfDay();
                if ($activity->lessThan($start)) {
                    return [
                        'valid' => false,
                        'message' => 'Tanggal kegiatan harus sama atau setelah ' . $start->format('Y-m-d') . '.',
                    ];
                }
            }

            return ['valid' => true, 'message' => ''];
        }

        $days = self::getSubmissionDateRangeDays();
        $earliestAllowed = $today->copy()->subDays($days);
        if ($activity->lessThan($earliestAllowed)) {
            return [
                'valid' => false,
                'message' => "Tanggal kegiatan melebihi batas {$days} hari dari hari ini.",
            ];
        }

        return ['valid' => true, 'message' => ''];
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
