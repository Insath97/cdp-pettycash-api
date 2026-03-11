<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\PermissionController;
use App\Http\Controllers\V1\RoleController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\DepartmentController;
use App\Http\Controllers\V1\EmployeeController;
use App\Http\Controllers\V1\ProvinceController;
use App\Http\Controllers\V1\RegionController;
use App\Http\Controllers\V1\ZonalController;
use App\Http\Controllers\V1\BranchController;
use App\Http\Controllers\V1\DesignationController;
use App\Http\Controllers\V1\PettyCashController;
use App\Http\Controllers\V1\ActivityLogController;
use Illuminate\Support\Facades\Route;

/* public routes */

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('petty-cashes', [PettyCashController::class, 'store']);
});

/* protected routes */
Route::middleware(['auth:api'])->prefix('v1')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('permissions/list', [PermissionController::class, 'getPermissionList']);
    Route::apiResource('permissions', PermissionController::class);

    Route::get('roles/list/', [RoleController::class, 'getAvailableRoles']);
    Route::apiResource('roles', RoleController::class);

    Route::patch('users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
    Route::apiResource('users', UserController::class);

    Route::patch('petty-cashes/{id}/status', [PettyCashController::class, 'updateStatus']);
    Route::patch('petty-cashes/{id}/payment-status', [PettyCashController::class, 'updatePaymentStatus']);
    Route::apiResource('petty-cashes', PettyCashController::class)->except(['store']);

    Route::get('activity-logs', [ActivityLogController::class, 'index']);
});
