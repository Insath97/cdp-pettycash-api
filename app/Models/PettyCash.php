<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCash extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'full_name',
        'email',
        'branch_id',
        'department_id',
        'date_needed',
        'category_id',
        'description',
        'type',
        'receipt_image_path',
        'amount',
        'status',
        'payment_status',
        'verified_by',
        'approved_by',
        'paid_by',
        'verified_description',
        'approved_description',
        'rejected_description',
        'payment_description',
        'account_number',
        'bank_name',
        'bank_branch',
    ];

    protected $casts = [
        'date_needed' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user who verified the petty cash.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the user who approved the petty cash.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who processed the payment for the petty cash.
     */
    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Get the branch associated with the petty cash.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the department associated with the petty cash.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the category associated with the petty cash.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
