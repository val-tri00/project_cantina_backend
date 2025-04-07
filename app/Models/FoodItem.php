<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    protected $fillable = ['name', 'price', 'calories', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
