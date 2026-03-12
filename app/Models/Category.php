<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
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
