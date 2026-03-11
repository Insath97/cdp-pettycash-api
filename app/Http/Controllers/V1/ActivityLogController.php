<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ActivityLogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            new Middleware('permission:Activity Log Index', only: ['index']),
        ];
    }

    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 20);
            $query = ActivityLog::with('user');

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('module')) {
                $query->where('module', $request->module);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $logs = $query->latest()->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Activity logs retrieved successfully',
                'data' => $logs
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve activity logs',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
