<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'trx_id',
        'client_id',
        'total_price',
        'total_discount',
        'grand_total',
        'status',
        'deadline',
        'bank_name',
        'account_number',
        'account_name',
        'transfer_amount',
        'transaction_type',
        'item_status',
        'payment_status',
        'customer_balance',
    ];

    protected $casts = [
        'deadline' => 'date',
        'customer_balance' => 'decimal:2',
    ];

    public const STATUSES = [
        'waiting for payment',
        'paid',
        'on progress',
        'done',
        'cancelled',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(TransactionPayment::class);
    }

    public function logs()
    {
        return $this->hasMany(TransactionLog::class)->orderBy('created_at', 'desc');
    }

    public function journalEntry()
    {
        return $this->hasOne(JournalEntry::class, 'reference_id')
            ->where('reference_type', 'transaction');
    }

    public static function generateTrxId(): string
    {
        $prefix = 'INV' . now()->format('my'); // e.g. INV0526 for May 2026

        // Find the last transaction created in the current month with this prefix
        $lastTrx = self::where('trx_id', 'like', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastTrx) {
            $parts = explode('-', $lastTrx->trx_id);
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $nextNumber = intval($parts[1]) + 1;
            }
        }

        return $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    protected static function booted()
    {
        static::creating(function ($transaction) {
            if (empty($transaction->trx_id) || str_starts_with($transaction->trx_id, 'TRX-')) {
                $transaction->trx_id = self::generateTrxId();
            }
        });
    }
}
