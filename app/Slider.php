<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'link',
        'image',
        'product_id',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
