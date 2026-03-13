<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use App\Traits\ActivityLogTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Database\Eloquent\Builder;

class BranchController extends Controller implements HasMiddleware
{
    use ActivityLogTrait;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:Branch Index', only: ['index']),
            new Middleware('permission:Branch Create', only: ['store']),
            new Middleware('permission:Branch Show', only: ['show']),
            new Middleware('permission:Branch Update', only: ['update']),
            new Middleware('permission:Branch Delete', only: ['destroy']),
            new Middleware('permission:Branch Toggle Status', only: ['toggleStatus']),
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
            $query = Branch::query();

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->has('is_active')) {
                $request->boolean('is_active') ? $query->where('is_active', true) : $query->where('is_active', false);
            }

            $branches = $query->latest()->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Branches retrieved successfully',
                'data' => $branches
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve branches',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBranchRequest $request)
    {
        try {
            $data = $request->validated();
            $branch = Branch::create($data);

            $this->logActivity('CREATE', 'branch', "Created branch with ID: {$branch->id}", $branch->toArray());

            return response()->json([
                'status' => 'success',
                'message' => 'Branch created successfully',
                'data' => $branch
            ], 201);
        } catch (\Throwable $th) {
            $this->logActivity('ERROR', 'branch', "Failed to create branch: " . substr($th->getMessage(), 0, 100));

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create branch',
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
            /** @var Branch|null $branch */
            $branch = Branch::where('id', $id)->first();

            if (!$branch) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Branch not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Branch retrieved successfully',
                'data' => $branch
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve branch',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBranchRequest $request, $id)
    {
        try {
            /** @var Branch|null $branch */
            $branch = Branch::where('id', $id)->first();

            if (!$branch) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Branch not found'
                ], 404);
            }

            $data = $request->validated();
            $branch->update($data);

            $this->logActivity('UPDATE', 'branch', "Updated branch with ID: {$branch->id}", $branch->toArray());

            return response()->json([
                'status' => 'success',
                'message' => 'Branch updated successfully',
                'data' => $branch
            ], 200);
        } catch (\Throwable $th) {
            $this->logActivity('ERROR', 'branch', "Failed to update branch ID {$id}: " . substr($th->getMessage(), 0, 100));

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update branch',
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
            /** @var Branch|null $branch */
            $branch = Branch::where('id', $id)->first();

            if (!$branch) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Branch not found'
                ], 404);
            }

            $branch->delete();

            $this->logActivity('DELETE', 'branch', "Deleted branch with ID: {$id}");

            return response()->json([
                'status' => 'success',
                'message' => 'Branch deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            $this->logActivity('ERROR', 'branch', "Failed to delete branch ID {$id}: " . substr($th->getMessage(), 0, 100));
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete branch',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Toggle the active status of the branch.
     */
    public function toggleStatus(string $id)
    {
        try {
            /** @var Branch|null $branch */
            $branch = Branch::where('id', $id)->first();

            if (!$branch) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Branch not found'
                ], 404);
            }

            $branch->is_active = !$branch->is_active;
            $branch->save();

            $this->logActivity('UPDATE_STATUS', 'branch', "Toggled status for branch ID: {$branch->id} to " . ($branch->is_active ? 'active' : 'inactive'));

            return response()->json([
                'status' => 'success',
                'message' => 'Branch status updated successfully',
                'data' => [
                    'id' => $branch->id,
                    'is_active' => $branch->is_active
                ]
            ], 200);
        } catch (\Throwable $th) {
            $this->logActivity('ERROR', 'branch', "Failed to toggle status for branch ID {$id}: " . substr($th->getMessage(), 0, 100));

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to toggle branch status',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get a listing of active branches for select box.
     */
    public function getBranchList()
    {
        try {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $query = Branch::query();
            $branches = $query->where('is_active', true)
                ->select('id', 'name', 'code')
                ->orderBy('name')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Active branches list retrieved successfully',
                'data' => $branches
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve branches list',
                'error' => config('app.debug') ? $th->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
