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

        $nowTimestamp = $this->created_at ?? $this->date ?? now();

        $journal = new \App\Models\JournalEntry([
            'date' => $this->date,
            'description' => $this->description,
            'reference_type' => 'cashbook',
            'reference_id' => $this->id,
        ]);
        $journal->created_at = $nowTimestamp;
        $journal->updated_at = $nowTimestamp;
        $journal->save();

        if ($this->type === 'money_in' || $this->type === 'in') {
            $jd1 = new \App\Models\JournalDetail([
                'journal_entry_id' => $journal->id,
                'account_id' => $this->account_id,
                'debit' => $this->amount,
                'credit' => 0,
            ]);
            $jd1->created_at = $nowTimestamp;
            $jd1->updated_at = $nowTimestamp;
            $jd1->save();

            $jd2 = new \App\Models\JournalDetail([
                'journal_entry_id' => $journal->id,
                'account_id' => $this->counter_account_id,
                'debit' => 0,
                'credit' => $this->amount,
            ]);
            $jd2->created_at = $nowTimestamp;
            $jd2->updated_at = $nowTimestamp;
            $jd2->save();
        } elseif ($this->type === 'transfer') {
            $jd1 = new \App\Models\JournalDetail([
                'journal_entry_id' => $journal->id,
                'account_id' => $this->account_id, // destination account
                'debit' => $this->amount,
                'credit' => 0,
            ]);
            $jd1->created_at = $nowTimestamp;
            $jd1->updated_at = $nowTimestamp;
            $jd1->save();

            $jd2 = new \App\Models\JournalDetail([
                'journal_entry_id' => $journal->id,
                'account_id' => $this->counter_account_id, // source account
                'debit' => 0,
                'credit' => $this->amount,
            ]);
            $jd2->created_at = $nowTimestamp;
            $jd2->updated_at = $nowTimestamp;
            $jd2->save();
        } elseif ($this->type === 'money_out' || $this->type === 'out') {
            // CREDIT (cash/bank)
            $jd1 = new \App\Models\JournalDetail([
                'journal_entry_id' => $journal->id,
                'account_id' => $this->account_id,
                'debit' => 0,
                'credit' => $this->amount,
            ]);
            $jd1->created_at = $nowTimestamp;
            $jd1->updated_at = $nowTimestamp;
            $jd1->save();

            // DEBIT (expense/account)
            $jd2 = new \App\Models\JournalDetail([
                'journal_entry_id' => $journal->id,
                'account_id' => $this->counter_account_id,
                'debit' => $this->amount,
                'credit' => 0,
            ]);
            $jd2->created_at = $nowTimestamp;
            $jd2->updated_at = $nowTimestamp;
            $jd2->save();
        }
    }
}
