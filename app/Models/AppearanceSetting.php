<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppearanceSetting extends Model
{
    protected $fillable = ['header_logo', 'favicon', 'bank_logo', 'bank_account_number', 'bank_account_name'];
}
