<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingHero extends Model
{
    protected $fillable = [
        'image',
        'link',
        'title',
        'subtitle',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
