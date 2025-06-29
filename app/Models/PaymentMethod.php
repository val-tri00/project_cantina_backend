<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_method';

    protected $fillable = [
        'id', 
        'method'
    ];

    public function orders() {
        return $this->hasMany(Order::class, 'payment_method_id');
    }
}
