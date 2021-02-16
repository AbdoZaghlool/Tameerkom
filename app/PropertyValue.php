<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropertyValue extends Model
{
    protected $fillable = ['property_id','name'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}