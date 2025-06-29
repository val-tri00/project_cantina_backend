<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    protected $fillable = ['name', 'price', 'calories', 'category_id', 'img_path'];
    protected $appends = ['image_url'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(){
        return $this->img_path
            ? url('/images/' . basename($this->img_path))
            : url('/images/default.png');
    }
}