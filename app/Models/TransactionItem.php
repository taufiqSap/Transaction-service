<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $table = 'transaction_item';

    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_name',
        'quantity',
        'subtotal'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}

