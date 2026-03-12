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
        'branch_location',
        'department',
        'date_needed',
        'category_id',
        'description',
        'type',
        'receipt_image_path',
        'amount',
        'status',
        'payment_status',
        'approved_by',
    ];

    protected $casts = [
        'date_needed' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user who approved the petty cash.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the category associated with the petty cash.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
