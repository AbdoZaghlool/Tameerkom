<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'details',
        'type_id',
        'price',
        'preparation_time',
        'category_id',
        'provider_id',
        'quantity',
        'sku',
        'image',
    ];

    protected $casts = [
        'views'=>'integer'
    ];

    // protected $dates =['start_at','end_at'];


    /**
     * get user rate
     *
     * @return Double $value;
     */
    public function getRateValue()
    {
        $value = ProductRate::where('product_id', $this->id)->avg('rate');
        return $value == 0 ? 0.1 : (double)$value;
    }

    public function additions()
    {
        return $this->hasMany(ProductAddition::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function pictures()
    {
        return $this->hasMany(Picture::class);
    }

    public function rates()
    {
        return $this->hasMany(ProductRate::class);
    }

    public function favourites()
    {
        return $this->morphToMany(Favourite::class, 'favourable');
    }

    /**
     * filter the model by name, take the sub cat or not
     *
     * @param QueryBiulder $query
     * @param Request $params
     * @return object
     */
    public function scopeStatus($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('status', $params);
        }
        return $query;
    }


    /**
     * filter the model by price
     *
     * @param QueryBiulder $query
     * @param Request $params
     * @return object
     */
    public function scopeFilter($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->orderBy($params, 'desc');
        }
        return $query;
    }

    /**
     * filter the model by price
     *
     * @param QueryBiulder $query
     * @param Request $params
     * @return object
     */
    public function scopeNotMe($query, $params)
    {
        //        dd($params);
        if (isset($params) && trim($params !== '')) {
            $query->where('provider_id', '!=', $params);
        }
        return $query;
    }
}