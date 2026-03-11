<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePettyCashRequest;
use App\Http\Requests\UpdatePettyCashStatusRequest;
use App\Http\Requests\UpdatePettyCashPaymentStatusRequest;
use App\Models\PettyCash;
use App\Traits\FileUploadTrait;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PettyCashController extends Controller implements HasMiddleware
{
    use FileUploadTrait, LogsActivity;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['store']),
            new Middleware('permission:Petty Cash Index', only: ['index', 'show']),
            new Middleware('permission:Petty Cash Update Status', only: ['updateStatus']),
            new Middleware('permission:Petty Cash Update Payment Status', only: ['updatePaymentStatus']),
            new Middleware('permission:Petty Cash Delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of petty cashes.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $query = PettyCash::with('approver');

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('reference_number', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('branch_location', 'like', "%{$search}%");
                });
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            $pettyCashes = $query->latest()->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Petty cashes retrieved successfully',
                'data' => $pettyCashes
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve petty cashes',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created petty cash in storage.
     */
    public function store(CreatePettyCashRequest $request)
    {
        try {
            $data = $request->validated();

            // Auto-generate Reference Number: PC-YYYYMMDD-XXXX
            $today = date('Ymd');
            $count = PettyCash::whereDate('created_at', today())->count() + 1;
            $data['reference_number'] = 'PC-' . $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // Conditional File Upload
            if ($data['type'] === 'reimbursement' && $request->hasFile('receipt_image_path')) {
                $imagePath = $this->handleFileUpload(
                    $request,
                    'receipt_image_path',
                    null,
                    'petty_cash/receipts',
                    $data['reference_number']
                );
                if ($imagePath) {
                    $data['receipt_image_path'] = $imagePath;
                }
            }

            $pettyCash = PettyCash::create($data);

            $this->logActivity('Submitted Petty Cash', 'Petty Cash', "Submitted petty cash: {$pettyCash->reference_number}", ['id' => $pettyCash->id]);

            Log::info('Petty Cash created', [
                'id' => $pettyCash->id,
                'reference_number' => $pettyCash->reference_number
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Petty cash submitted successfully',
                'data' => $pettyCash
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit petty cash',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified petty cash.
     */
    public function show(string $id)
    {
        try {
            $pettyCash = PettyCash::with('approver')->find($id);

            if (!$pettyCash) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Petty cash not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Petty cash retrieved successfully',
                'data' => $pettyCash
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve petty cash',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the status of the petty cash (Approve/Reject).
     */
    public function updateStatus(UpdatePettyCashStatusRequest $request, string $id)
    {
        try {
            $pettyCash = PettyCash::find($id);

            if (!$pettyCash) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Petty cash not found'
                ], 404);
            }

            $data = $request->validated();
            $data['approved_by'] = Auth::id();

            $pettyCash->update($data);

            $this->logActivity('Updated Petty Cash Status', 'Petty Cash', "Updated status for {$pettyCash->reference_number} to {$pettyCash->status}", ['id' => $pettyCash->id, 'status' => $pettyCash->status]);

            Log::info('Petty Cash status updated', [
                'id' => $pettyCash->id,
                'status' => $pettyCash->status,
                'approver_id' => Auth::id()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Petty cash status updated successfully',
                'data' => $pettyCash->load('approver')
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update petty cash status',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the payment status of the petty cash (Pending/On-Hold/Paid).
     */
    public function updatePaymentStatus(UpdatePettyCashPaymentStatusRequest $request, string $id)
    {
        try {
            $pettyCash = PettyCash::find($id);

            if (!$pettyCash) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Petty cash not found'
                ], 404);
            }

            // Optional: Business logic, maybe only allow payment status update if approved?
            // if ($pettyCash->status !== 'approved') {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'Cannot update payment status for non-approved petty cash'
            //     ], 422);
            // }

            $data = $request->validated();
            $pettyCash->update($data);

            $this->logActivity('Updated Petty Cash Payment Status', 'Petty Cash', "Updated payment status for {$pettyCash->reference_number} to {$pettyCash->payment_status}", ['id' => $pettyCash->id, 'payment_status' => $pettyCash->payment_status]);

            Log::info('Petty Cash payment status updated', [
                'id' => $pettyCash->id,
                'payment_status' => $pettyCash->payment_status,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Petty cash payment status updated successfully',
                'data' => $pettyCash
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update petty cash payment status',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified petty cash from storage.
     */
    public function destroy(string $id)
    {
        try {
            $pettyCash = PettyCash::find($id);

            if (!$pettyCash) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Petty cash not found'
                ], 404);
            }

            // Only allow deletion if pending
            if ($pettyCash->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only pending petty cash requests can be deleted'
                ], 422);
            }

            $this->deleteFile($pettyCash->receipt_image_path);
            $pettyCash->delete();

            $this->logActivity('Deleted Petty Cash', 'Petty Cash', "Deleted petty cash: {$id}");

            Log::info('Petty Cash deleted', [
                'id' => $id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Petty cash deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete petty cash',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
