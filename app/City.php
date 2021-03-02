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

    /**
     * filter the model by name, take the sub cat or not
     *
     * @param QueryBiulder $query
     * @param Request $params
     * @return object
     */
    public function scopeFilter($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('region_id', $params);
        }
        return $query;
    }
}