<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get the petty cash requests for the category.
     */
    public function pettyCashes()
    {
        return $this->hasMany(PettyCash::class);
    }
}
