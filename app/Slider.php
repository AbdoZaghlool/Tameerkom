<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'link',
        'image',
        'provider_id',
    ];


    public function family()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
