<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use App\Traits\ActivityLogTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Database\Eloquent\Builder;

class DepartmentController extends Controller implements HasMiddleware
{
    use ActivityLogTrait;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:Department Index', only: ['index']),
            new Middleware('permission:Department Create', only: ['store']),
            new Middleware('permission:Department Show', only: ['show']),
            new Middleware('permission:Department Update', only: ['update']),
            new Middleware('permission:Department Delete', only: ['destroy']),
            new Middleware('permission:Department Toggle Status', only: ['toggleStatus']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 20);

            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $query = Department::query();

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('department_head_name', 'like', "%{$search}%")
                        ->orWhere('department_head_email', 'like', "%{$search}%");
                });
            }

            if ($request->has('is_active')) {
                $request->boolean('is_active') ? $query->where('is_active', true) : $query->where('is_active', false);
            }

            $departments = $query->latest()->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Departments retrieved successfully',
                'data' => $departments
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve departments',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDepartmentRequest $request)
    {
        try {
            $data = $request->validated();
            $department = Department::create($data);

            $this->logActivity('CREATE', 'department', "Created department with ID: {$department->id}", $department->toArray());

            return response()->json([
                'status' => 'success',
                'message' => 'Department created successfully',
                'data' => $department
            ], 201);
        } catch (\Throwable $th) {
            $this->logActivity('ERROR', 'department', "Failed to create department: " . substr($th->getMessage(), 0, 100));

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create department',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            /** @var Department|null $department */
            $department = Department::where('id', $id)->first();

            if (!$department) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Department not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Department retrieved successfully',
                'data' => $department
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve department',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, $id)
    {
        try {
            /** @var Department|null $department */
            $department = Department::where('id', $id)->first();

            if (!$department) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Department not found'
                ], 404);
            }

            $data = $request->validated();
            $department->update($data);

            $this->logActivity('UPDATE', 'department', "Updated department with ID: {$department->id}", $department->toArray());

            return response()->json([
                'status' => 'success',
                'message' => 'Department updated successfully',
                'data' => $department
            ], 200);
        } catch (\Throwable $th) {
            $this->logActivity('ERROR', 'department', "Failed to update department ID {$id}: " . substr($th->getMessage(), 0, 100));

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update department',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            /** @var Department|null $department */
            $department = Department::where('id', $id)->first();

            if (!$department) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Department not found'
                ], 404);
            }

            $department->delete();

            $this->logActivity('DELETE', 'department', "Deleted department with ID: {$id}");

            return response()->json([
                'status' => 'success',
                'message' => 'Department deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            $this->logActivity('ERROR', 'department', "Failed to delete department ID {$id}: " . substr($th->getMessage(), 0, 100));

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete department',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Toggle the active status of the department.
     */
    public function toggleStatus(string $id)
    {
        try {
            /** @var Department|null $department */
            $department = Department::where('id', $id)->first();

            if (!$department) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Department not found'
                ], 404);
            }

            $department->is_active = !$department->is_active;
            $department->save();

            $this->logActivity('UPDATE_STATUS', 'department', "Toggled status for department ID: {$department->id} to " . ($department->is_active ? 'active' : 'inactive'));

            return response()->json([
                'status' => 'success',
                'message' => 'Department status updated successfully',
                'data' => [
                    'id' => $department->id,
                    'is_active' => $department->is_active
                ]
            ], 200);
        } catch (\Throwable $th) {
            $this->logActivity('ERROR', 'department', "Failed to toggle status for department ID {$id}: " . substr($th->getMessage(), 0, 100));

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to toggle department status',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get a listing of active departments for select box.
     */
    public function getDepartmentList()
    {
        try {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $query = Department::query();
            $departments = $query->where('is_active', true)
                ->select('id', 'name', 'code')
                ->orderBy('name')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Active departments list retrieved successfully',
                'data' => $departments
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve departments list',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
