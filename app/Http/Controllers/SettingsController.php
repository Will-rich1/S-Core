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

    /**
     * Get security PIN
     */
    public function getSecurityPin()
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pin = SystemSetting::getSetting('security_pin', '123456');
        
        return response()->json([
            'pin' => $pin
        ]);
    }

    /**
     * Update security PIN
     */
    public function updateSecurityPin(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->merge([
            'current_pin' => trim((string) $request->input('current_pin', '')),
            'new_pin' => trim((string) $request->input('new_pin', '')),
            'new_pin_confirmation' => trim((string) $request->input('new_pin_confirmation', '')),
        ]);

        // Validate input
        $validated = $request->validate([
            'current_pin' => 'required|string|min:4|max:6',
            'new_pin' => 'required|string|min:4|max:6|regex:/^[0-9]{4,6}$/',
            'new_pin_confirmation' => 'required|same:new_pin'
        ]);

        try {
            // Get current PIN from database
            $currentStoredPin = trim((string) SystemSetting::getSetting('security_pin', '123456'));
            $currentInputPin = trim((string) $validated['current_pin']);
            $newPin = trim((string) $validated['new_pin']);

            // Verify current PIN
            if ($currentInputPin !== $currentStoredPin) {
                return response()->json([
                    'message' => 'Current PIN is incorrect'
                ], 400);
            }

            // Update PIN
            SystemSetting::setSetting(
                'security_pin',
                $newPin,
                'string',
                'Security PIN for category management',
                Auth::id()
            );

            return response()->json([
                'message' => 'Security PIN updated successfully',
                'data' => [
                    'updated' => true
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update security PIN',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify security PIN
     */
    public function verifySecurityPin(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->merge([
            'pin' => trim((string) $request->input('pin', '')),
        ]);

        $validated = $request->validate([
            'pin' => 'required|string'
        ]);

        $correctPin = trim((string) SystemSetting::getSetting('security_pin', '123456'));
        $inputPin = trim((string) $validated['pin']);

        if ($inputPin === $correctPin) {
            return response()->json([
                'valid' => true,
                'message' => 'PIN verified successfully'
            ]);
        } else {
            return response()->json([
                'valid' => false,
                'message' => 'Incorrect PIN'
            ], 400);
        }
    }

    /**
     * Reset security PIN (Admin only - no current PIN required)
     */
    public function resetPin(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->merge([
            'new_pin' => trim((string) $request->input('new_pin', '')),
        ]);

        // Validate input
        $validated = $request->validate([
            'new_pin' => 'required|string|min:4|max:6|regex:/^[0-9]{4,6}$/'
        ]);

        try {
            // Update PIN directly without checking current PIN
            SystemSetting::setSetting(
                'security_pin',
                $validated['new_pin'],
                'string',
                'Security PIN for category management',
                Auth::id()
            );

            return response()->json([
                'message' => 'Security PIN has been reset successfully',
                'data' => [
                    'updated' => true
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reset security PIN',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
