<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BankTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'from_account_id',
        'to_account_id',
        'amount',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($bankTransfer) {
            if ($bankTransfer->from_account_id == $bankTransfer->to_account_id) {
                throw new \Exception('From Account and To Account cannot be the same.');
            }
        });

        static::created(function ($bankTransfer) {
            DB::transaction(function () use ($bankTransfer) {
                // Auto create journal_entries
                $journal = JournalEntry::create([
                    'date' => $bankTransfer->date,
                    'description' => 'Bank Transfer',
                    'reference_type' => 'bank_transfer',
                    'reference_id' => $bankTransfer->id,
                ]);

                // DEBIT (Destination Account)
                JournalDetail::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $bankTransfer->to_account_id,
                    'debit' => $bankTransfer->amount,
                    'credit' => 0,
                    'created_at' => $bankTransfer->date,
                    'updated_at' => $bankTransfer->date,
                ]);

                // CREDIT (Source Account)
                JournalDetail::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $bankTransfer->from_account_id,
                    'debit' => 0,
                    'credit' => $bankTransfer->amount,
                    'created_at' => $bankTransfer->date,
                    'updated_at' => $bankTransfer->date,
                ]);
            });
        });

        static::deleting(function ($bankTransfer) {
            DB::transaction(function () use ($bankTransfer) {
                if ($bankTransfer->journalEntry) {
                    $bankTransfer->journalEntry->delete();
                }
            });
        });
    }

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function journalEntry()
    {
        return $this->hasOne(JournalEntry::class, 'reference_id')
            ->where('reference_type', 'bank_transfer');
    }
}
