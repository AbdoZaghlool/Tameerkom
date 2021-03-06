<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductRate extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'rate',
        'notes',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
