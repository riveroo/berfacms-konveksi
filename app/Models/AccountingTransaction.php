<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingTransaction extends Model
{
    use HasFactory;

    protected $table = 'accounting_transactions';

    protected $fillable = [
        'type',
        'date',
        'amount',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function journalEntry()
    {
        return $this->hasOne(JournalEntry::class, 'reference_id')
            ->where('reference_type', 'accounting_transaction');
    }
}
