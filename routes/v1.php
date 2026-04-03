<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\PermissionController;
use App\Http\Controllers\V1\RoleController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\PettyCashController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\BranchController;
use App\Http\Controllers\V1\DepartmentController;
use App\Http\Controllers\V1\NotificationController;
use App\Http\Controllers\V1\ActivityLogController;
use App\Http\Controllers\V1\SystemSettingController;
use Illuminate\Support\Facades\Route;

/* public routes */

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:auth');
    Route::post('petty-cashes', [PettyCashController::class, 'store'])->middleware('throttle:api');
    Route::get('categories/public/list', [CategoryController::class, 'getCategoryList']);
    Route::get('branches/public/list', [BranchController::class, 'getBranchList']);
    Route::get('departments/public/list', [DepartmentController::class, 'getDepartmentList']);
    Route::get('system-settings', [SystemSettingController::class, 'index']);
});

/* protected routes */
Route::middleware(['auth:api', 'throttle:api'])->prefix('v1')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('permissions/list', [PermissionController::class, 'getPermissionList']);
    Route::apiResource('permissions', PermissionController::class);

    Route::get('roles/list/', [RoleController::class, 'getAvailableRoles']);
    Route::apiResource('roles', RoleController::class);

    Route::patch('users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
    Route::apiResource('users', UserController::class);

    Route::get('categories/list', [CategoryController::class, 'getCategoryList']);
    Route::apiResource('categories', CategoryController::class);

    Route::get('branches/list', [BranchController::class, 'getBranchList']);
    Route::patch('branches/{id}/toggle-status', [BranchController::class, 'toggleStatus']);
    Route::apiResource('branches', BranchController::class);

    Route::get('departments/list', [DepartmentController::class, 'getDepartmentList']);
    Route::patch('departments/{id}/toggle-status', [DepartmentController::class, 'toggleStatus']);
    Route::apiResource('departments', DepartmentController::class);

    Route::patch('petty-cashes/{id}/verify', [PettyCashController::class, 'updateStatusVerified']);
    Route::patch('petty-cashes/{id}/approve', [PettyCashController::class, 'updateStatusApproved']);
    Route::patch('petty-cashes/{id}/payment-status', [PettyCashController::class, 'updatePaymentStatus']);
    Route::apiResource('petty-cashes', PettyCashController::class)->except(['store']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread', [NotificationController::class, 'unread']);
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    Route::get('activity-logs', [ActivityLogController::class, 'index']);

    Route::patch('system-settings', [SystemSettingController::class, 'update']);
});
