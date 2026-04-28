<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingValue extends Model
{
    protected $fillable = [
        'image',
        'title',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
