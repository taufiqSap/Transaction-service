<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transaction';
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'total_price',
        'amount_paid',
        'change'
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
