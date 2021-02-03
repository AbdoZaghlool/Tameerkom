<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'region_id'];

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function providers()
    {
        return $this->hasMany(User::class);
    }
}
