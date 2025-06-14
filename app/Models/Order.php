<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'order_status_id',
        'payment_method_id',
        'total_price',
        'pickup_time',
        'created_at'
    ];

    public function items(){
        return $this->hasMany(OrderItem::class);
    }
}
