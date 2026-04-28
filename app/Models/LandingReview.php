<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingReview extends Model
{
    protected $fillable = [
        'review_text',
        'reviewer_name',
        'client_name',
        'sort_order',
    ];
}
