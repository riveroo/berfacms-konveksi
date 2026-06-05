<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountMonthlyBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'period_year',
        'period_month',
        'opening_balance',
        'debit_total',
        'credit_total',
        'closing_balance',
        'is_dirty',
        'generated_at',
    ];

    protected $casts = [
        'period_year' => 'integer',
        'period_month' => 'integer',
        'opening_balance' => 'decimal:2',
        'debit_total' => 'decimal:2',
        'credit_total' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'is_dirty' => 'boolean',
        'generated_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
