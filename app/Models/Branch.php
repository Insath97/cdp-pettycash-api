<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address_line1',
        'city',
        'postal_code',
        'phone_primary',
        'phone_secondary',
        'email',
        'fax',
        'branch_head_name',
        'branch_head_email',
        'branch_head_phone',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the petty cash requests for the branch.
     */
    public function pettyCashes()
    {
        return $this->hasMany(PettyCash::class);
    }
}
