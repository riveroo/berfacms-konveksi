<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingCategory extends Model
{
    protected $fillable = ['image', 'title', 'link', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
