<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carts extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'product_id',
        'qty',
        'price',
        'sub_total',
        'discount',
        'total',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transactions::class, 'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
