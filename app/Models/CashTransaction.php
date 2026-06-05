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
            ->where('reference_type', 'cashbook');
    }

    public function generateJournal()
    {
        if ($this->journalEntry) {
            $this->journalEntry->delete();
        }

        $journal = \App\Models\JournalEntry::create([
            'date' => $this->date,
            'description' => $this->description,
            'reference_type' => 'cashbook',
            'reference_id' => $this->id,
        ]);

        if ($this->type === 'money_in' || $this->type === 'in') {
            \App\Models\JournalDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $this->account_id,
                'debit' => $this->amount,
                'credit' => 0,
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);
            \App\Models\JournalDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $this->counter_account_id,
                'debit' => 0,
                'credit' => $this->amount,
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);
        } elseif ($this->type === 'transfer') {
            \App\Models\JournalDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $this->account_id, // destination account
                'debit' => $this->amount,
                'credit' => 0,
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);
            \App\Models\JournalDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $this->counter_account_id, // source account
                'debit' => 0,
                'credit' => $this->amount,
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);
        } elseif ($this->type === 'money_out' || $this->type === 'out') {
            // CREDIT (cash/bank)
            \App\Models\JournalDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $this->account_id,
                'debit' => 0,
                'credit' => $this->amount,
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);
            // DEBIT (expense/account)
            \App\Models\JournalDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $this->counter_account_id,
                'debit' => $this->amount,
                'credit' => 0,
                'created_at' => $this->date,
                'updated_at' => $this->date,
            ]);
        }
    }
}
