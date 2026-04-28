<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingFooter extends Model
{
    protected $fillable = [
        'company_name',
        'address',
        'phone',
        'email',
        'youtube_url',
        'instagram_url',
        'tiktok_url',
        'tokopedia_url',
        'shopee_url',
        'facebook_url',
    ];
}
