<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\FoodItem;

class Category extends Model{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = ['name'];

    public function foodItems(){
        return $this->hasMany(FoodItem::class, 'category_id');
    }
}