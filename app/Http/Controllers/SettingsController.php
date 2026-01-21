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
            'minCategories' => SCoreHelper::getMinCategoriesRequired()
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
            'minCategories' => 'required|integer|min:1|max:10'
        ]);

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

            return response()->json([
                'message' => 'Settings updated successfully',
                'data' => [
                    'minPoints' => $validated['minPoints'],
                    'minCategories' => $validated['minCategories']
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

        // Validate input
        $validated = $request->validate([
            'current_pin' => 'required|string|min:4|max:6',
            'new_pin' => 'required|string|min:4|max:6|regex:/^[0-9]{4,6}$/',
            'new_pin_confirmation' => 'required|same:new_pin'
        ]);

        try {
            // Get current PIN from database
            $currentStoredPin = SystemSetting::getSetting('security_pin', '123456');

            // Verify current PIN
            if ($validated['current_pin'] !== $currentStoredPin) {
                return response()->json([
                    'message' => 'Current PIN is incorrect'
                ], 400);
            }

            // Update PIN
            SystemSetting::setSetting(
                'security_pin',
                $validated['new_pin'],
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

        $validated = $request->validate([
            'pin' => 'required|string'
        ]);

        $correctPin = SystemSetting::getSetting('security_pin', '123456');

        if ($validated['pin'] === $correctPin) {
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
