<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class NotificationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Notification Index', only: ['index', 'unread']),
            new Middleware('permission:Notification Mark Read', only: ['markAsRead']),
            new Middleware('permission:Notification Mark All Read', only: ['markAllAsRead']),
        ];
    }

    /**
     * Display a listing of the notifications.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 20);
            $notifications = Auth::user()
                ->notifications()
                ->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Notifications retrieved successfully',
                'data' => $notifications
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve notifications',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread notifications.
     */
    public function unread()
    {
        try {
            $notifications = Auth::user()->unreadNotifications;

            return response()->json([
                'status' => 'success',
                'message' => 'Unread notifications retrieved successfully',
                'data' => $notifications
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve unread notifications',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(string $id)
    {
        try {
            $notification = Auth::user()
                ->notifications()
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'status' => 'success',
                'message' => 'Notification marked as read'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark notification as read',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        try {
            Auth::user()->unreadNotifications->markAsRead();

            return response()->json([
                'status' => 'success',
                'message' => 'All notifications marked as read'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark all notifications as read',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
