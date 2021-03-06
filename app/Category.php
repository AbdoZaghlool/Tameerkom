<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'image', 'min_quantity'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
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
            $query->where('id', $params);
        }
        return $query;
    }
}