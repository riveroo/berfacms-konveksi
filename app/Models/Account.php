<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'subtype',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalDetails()
    {
        return $this->hasMany(JournalDetail::class);
    }

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class, 'account_id');
    }

    public function counterCashTransactions()
    {
        return $this->hasMany(CashTransaction::class, 'counter_account_id');
    }
}
