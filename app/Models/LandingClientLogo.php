<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingClientLogo extends Model
{
    protected $fillable = ['image', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
