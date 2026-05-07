<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'is_active',
        'version',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function lines()
    {
        return $this->hasMany(TransactionTemplateLine::class, 'template_id');
    }
}
