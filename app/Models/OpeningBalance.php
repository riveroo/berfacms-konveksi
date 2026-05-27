<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OpeningBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'account_id',
        'counter_account_id',
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

        static::saving(function ($openingBalance) {
            if ($openingBalance->account_id == $openingBalance->counter_account_id) {
                throw new \Exception('Account and Counter Account cannot be the same.');
            }

            $exists = self::where('account_id', $openingBalance->account_id)
                ->where('id', '!=', $openingBalance->id)
                ->exists();
            if ($exists) {
                throw new \Exception('This account already has an opening balance.');
            }
        });

        static::created(function ($openingBalance) {
            DB::transaction(function () use ($openingBalance) {
                // Auto create journal_entries
                $journal = JournalEntry::create([
                    'date' => $openingBalance->date,
                    'description' => 'opening balance',
                    'reference_type' => 'opening_balance',
                    'reference_id' => $openingBalance->id,
                ]);

                // DEBIT
                JournalDetail::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $openingBalance->account_id,
                    'debit' => $openingBalance->amount,
                    'credit' => 0,
                    'created_at' => $openingBalance->date,
                    'updated_at' => $openingBalance->date,
                ]);

                // CREDIT
                JournalDetail::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $openingBalance->counter_account_id,
                    'debit' => 0,
                    'credit' => $openingBalance->amount,
                    'created_at' => $openingBalance->date,
                    'updated_at' => $openingBalance->date,
                ]);
            });
        });

        static::updated(function ($openingBalance) {
            DB::transaction(function () use ($openingBalance) {
                // Delete old journal
                if ($openingBalance->journalEntry) {
                    $openingBalance->journalEntry->delete();
                }

                // Create new journal
                $journal = JournalEntry::create([
                    'date' => $openingBalance->date,
                    'description' => 'opening balance',
                    'reference_type' => 'opening_balance',
                    'reference_id' => $openingBalance->id,
                ]);

                // DEBIT
                JournalDetail::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $openingBalance->account_id,
                    'debit' => $openingBalance->amount,
                    'credit' => 0,
                    'created_at' => $openingBalance->date,
                    'updated_at' => $openingBalance->date,
                ]);

                // CREDIT
                JournalDetail::create([
                    'journal_entry_id' => $journal->id,
                    'account_id' => $openingBalance->counter_account_id,
                    'debit' => 0,
                    'credit' => $openingBalance->amount,
                    'created_at' => $openingBalance->date,
                    'updated_at' => $openingBalance->date,
                ]);
            });
        });

        static::deleting(function ($openingBalance) {
            DB::transaction(function () use ($openingBalance) {
                if ($openingBalance->journalEntry) {
                    $openingBalance->journalEntry->delete();
                }
            });
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function counterAccount()
    {
        return $this->belongsTo(Account::class, 'counter_account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function journalEntry()
    {
        return $this->hasOne(JournalEntry::class, 'reference_id')
            ->where('reference_type', 'opening_balance');
    }
}
