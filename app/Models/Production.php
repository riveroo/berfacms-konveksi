<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_date',
        'batch_code',
        'production_name',
        'user_id',
    ];

    protected $casts = [
        'production_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function materials()
    {
        return $this->hasMany(ProductionMaterial::class);
    }

    public function products()
    {
        return $this->hasMany(ProductionProduct::class);
    }
}
