<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable //implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'brief',
        'type', // 0=>user, 1=>provider
        'phone_number',
        'email',
        'commercial_record',
        'city_id',
        'image',
        'latitude',
        'longitude',
        'password',
        'active',
        'available',
        'verified',
        'api_token',
        'verification_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * get user type in string instead of numeric value
     *
     * @return String $type
     */
    public function getType()
    {
        $type = $this->type == '0'? 'عميل' :'مزود خدمة';
        return $type;
    }

    /**
     * get user rate
     *
     * @return Double $value;
     */
    public function getRateValue()
    {
        $value = Rate::where('to_user_id', $this->id)->avg('rate');
        return $value == 0 ? 0.1 : (double)$value;
    }

    /**
     * relation for family orders
     *
     * @return void
     */
    public function providerOrders()
    {
        return $this->hasMany(Order::class, 'provider_id');
    }

    /**
     * relation for customer orders
     *
     * @return void
     */
    public function userOrders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function histories()
    {
        return $this->hasMany(History::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'provider_id');
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function favouritUsers()
    {
        return $this->morphToMany(Favourite::class, 'favourable');
    }

    public function favouritProducts()
    {
        return $this->morphToMany(Favourite::class, 'favourable');
    }
}