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
        'bank_name',
        'account_number',
        'account_name',
        'transfer_amount',
        'transaction_type',
        'item_status',
        'payment_status',
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
}
