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
}
