<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'order_id',
        'rate',
        'comment',
    ];



    public function rateFrom()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function rateTo()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}