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
        'description',
        'logo',
    ];

    public function getFormattedWaNumberAttribute()
    {
        $waNumber = preg_replace('/[^0-9]/', '', $this->phone);
        if (str_starts_with($waNumber, '0')) {
            $waNumber = '62' . substr($waNumber, 1);
        }
        return $waNumber;
    }

    public function getWaLink($message = '')
    {
        $number = $this->formatted_wa_number;
        $link = "https://wa.me/{$number}";
        if ($message) {
            $link .= "?text=" . urlencode($message);
        }
        return $link;
    }
}
