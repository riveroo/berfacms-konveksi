<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionTemplateLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'account_id',
        'position',
        'amount_source',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function template()
    {
        return $this->belongsTo(TransactionTemplate::class, 'template_id');
    }
}
