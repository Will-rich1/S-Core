<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemSetting;
use App\Helpers\SCoreHelper;

class SettingsController extends Controller
{
    /**
     * Get all S-Core settings
     */
    public function getScoreSettings()
    {
        return response()->json([
            'minPoints' => SCoreHelper::getMinPointsRequired(),
            'minCategories' => SCoreHelper::getMinCategoriesRequired(),
            'perfectMinPoints' => SCoreHelper::getPerfectMinPointsRequired(),
            'submissionDateRuleMode' => SCoreHelper::getSubmissionDateRuleMode(),
            'submissionDateRangeDays' => SCoreHelper::getSubmissionDateRangeDays(),
            'submissionStartDate' => SCoreHelper::getSubmissionStartDate(),
            'maintenanceMode' => SCoreHelper::isStudentMaintenanceModeEnabled(),
        ]);
    }

    /**
     * Update S-Core settings
     */
    public function updateScoreSettings(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate input
        $validated = $request->validate([
            'minPoints' => 'required|integer|min:1|max:1000',
            'minCategories' => 'required|integer|min:1|max:10',
            'perfectMinPoints' => 'nullable|integer|min:1|max:1000',
            'submissionDateRuleMode' => 'required|in:rolling_days,fixed_start_date',
            'submissionDateRangeDays' => 'nullable|integer|min:1|max:3650',
            'submissionStartDate' => 'nullable|date',
            'maintenanceMode' => 'nullable|boolean',
        ]);

        if ($validated['submissionDateRuleMode'] === 'rolling_days' && empty($validated['submissionDateRangeDays'])) {
            return response()->json([
                'message' => 'Jumlah hari wajib diisi saat mode rentang hari dipilih.'
            ], 422);
        }

        if ($validated['submissionDateRuleMode'] === 'fixed_start_date' && empty($validated['submissionStartDate'])) {
            return response()->json([
                'message' => 'Tanggal mulai wajib diisi saat mode tanggal mulai dipilih.'
            ], 422);
        }

        try {
            // Update settings
            SystemSetting::setSetting(
                'min_points_required',
                $validated['minPoints'],
                'integer',
                'Minimum points required for S-Core eligibility',
                Auth::id()
            );

            SystemSetting::setSetting(
                'min_categories_required',
                $validated['minCategories'],
                'integer',
                'Minimum categories required for S-Core eligibility',
                Auth::id()
            );

            if (array_key_exists('perfectMinPoints', $validated) && $validated['perfectMinPoints'] !== null) {
                SystemSetting::setSetting(
                    'perfect_min_points',
                    $validated['perfectMinPoints'],
                    'integer',
                    'Minimum points required for Perfect students list',
                    Auth::id()
                );
            }

            SystemSetting::setSetting(
                'submission_date_rule_mode',
                $validated['submissionDateRuleMode'],
                'string',
                'Submission date rule mode: rolling_days or fixed_start_date',
                Auth::id()
            );

            if ($validated['submissionDateRuleMode'] === 'rolling_days') {
                SystemSetting::setSetting(
                    'submission_date_range_days',
                    (int) $validated['submissionDateRangeDays'],
                    'integer',
                    'Activity date must be within X days from today',
                    Auth::id()
                );
            }

            if ($validated['submissionDateRuleMode'] === 'fixed_start_date') {
                SystemSetting::setSetting(
                    'submission_start_date',
                    $validated['submissionStartDate'],
                    'string',
                    'Activity date must be on/after this date',
                    Auth::id()
                );
            }

            SystemSetting::setSetting(
                'student_maintenance_mode',
                (bool) ($validated['maintenanceMode'] ?? false),
                'boolean',
                'Student login is blocked and redirected to maintenance page when enabled',
                Auth::id()
            );

            return response()->json([
                'message' => 'Settings updated successfully',
                'data' => [
                    'minPoints' => $validated['minPoints'],
                    'minCategories' => $validated['minCategories'],
                    'perfectMinPoints' => $validated['perfectMinPoints'] ?? SCoreHelper::getPerfectMinPointsRequired(),
                    'submissionDateRuleMode' => SCoreHelper::getSubmissionDateRuleMode(),
                    'submissionDateRangeDays' => SCoreHelper::getSubmissionDateRangeDays(),
                    'submissionStartDate' => SCoreHelper::getSubmissionStartDate(),
                    'maintenanceMode' => SCoreHelper::isStudentMaintenanceModeEnabled(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update minimum points for Perfect list page
     */
    public function updatePerfectPoints(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate([
            'perfect_min_points' => 'required|integer|min:1|max:1000',
        ]);

        SystemSetting::setSetting(
            'perfect_min_points',
            $validated['perfect_min_points'],
            'integer',
            'Minimum points required for Perfect students list',
            Auth::id()
        );

        return redirect()->back()->with('success', 'Minimum poin Perfect berhasil diperbarui.');
    }

    /**
     * Get all system settings
     */
    public function getAllSettings()
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $settings = SystemSetting::all();
        return response()->json($settings);
    }

}
