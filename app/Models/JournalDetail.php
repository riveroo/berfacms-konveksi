<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit',
        'credit',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    protected static function booted()
    {
        static::saved(function ($detail) {
            $detail->loadMissing('journalEntry');
            if ($detail->journalEntry) {
                app(\App\Services\Accounting\MonthlyBalanceService::class)->markAsDirtyFrom($detail->journalEntry->date);
            }
        });

        static::deleted(function ($detail) {
            $detail->loadMissing('journalEntry');
            if ($detail->journalEntry) {
                app(\App\Services\Accounting\MonthlyBalanceService::class)->markAsDirtyFrom($detail->journalEntry->date);
            }
        });
    }
}
