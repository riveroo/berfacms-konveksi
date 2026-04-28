<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingBannerCta extends Model
{
    protected $fillable = ['title', 'description', 'image', 'link', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
