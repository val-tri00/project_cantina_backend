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

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items(){
        return $this->hasMany(OrderItem::class);
    }

    public function paymentMethod() {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function status() {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }
}

