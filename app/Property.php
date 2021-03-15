<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'category_id', 'name'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function values()
    {
        return $this->hasMany(PropertyValue::class);
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
            $query->where('category_id', $params);
        }
        return $query;
    }
}