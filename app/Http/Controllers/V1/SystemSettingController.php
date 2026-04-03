<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SystemSettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['index']),
            new Middleware('permission:System Setting Update', only: ['update']),
        ];
    }

    /**
     * Display a listing of system settings.
     */
    public function index()
    {
        try {
            $settings = SystemSetting::all();
            return response()->json([
                'status' => 'success',
                'message' => 'System settings retrieved successfully',
                'data' => $settings
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve system settings',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update system settings.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->settings as $setting) {
                SystemSetting::updateOrCreate(
                    ['key' => $setting['key']],
                    ['value' => $setting['value']]
                );
            }

            return response()->json([
                'status' => 'success',
                'message' => 'System settings updated successfully',
                'data' => SystemSetting::all()
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update system settings',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
