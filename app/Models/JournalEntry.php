<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'description',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(JournalDetail::class);
    }

    public function cashTransaction()
    {
        return $this->belongsTo(CashTransaction::class, 'reference_id')
            ->where('reference_type', 'cash_transaction');
    }
}
