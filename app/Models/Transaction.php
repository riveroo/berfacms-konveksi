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
}
