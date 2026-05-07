<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CashTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'description',
        'type',
        'amount',
        'client_id',
        'receive_from',
        'account_id',
        'counter_account_id',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function counterAccount()
    {
        return $this->belongsTo(Account::class, 'counter_account_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function journalEntry()
    {
        return $this->hasOne(JournalEntry::class, 'reference_id')
            ->where('reference_type', 'cash_transaction');
    }
}
