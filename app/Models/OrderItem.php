<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'food_id',
        'quantity',
        'price'
    ];

    public function food(){
        return $this->belongsTo(FoodItem::class, 'food_id');
    }
}
