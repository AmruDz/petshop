<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $fillable = [
        'cashier_id',
        'date',
        'transaction_number',
        'customer',
        'sub_total',
        'discount',
        'total',
    ];
    
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'transaction_id');
    }
}
