<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
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
        'type', // 0=>user, 1=>family, 2=>driver
        'phone_number',
        'email',
        'region_id',
        'city_id',
        'image',
        'latitude',
        'longitude',
        'password',
        'tax_number',
        'views', // fro the family views numbers
        'bank_name',
        'bank_user_name',
        'account_number',
        'insurance_number',
        'driver_license',
        'type_id', // for driver to choose delivery type
        'car_license',
        'identity_number',
        'work_start_at',
        'work_end_at',
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
        $type = $this->type == '0'? 'عميل' :($this->type == '1' ? 'اسرة منتجة' : 'سائق');
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
    public function familyOrders()
    {
        return $this->hasMany(Order::class, 'provider_id');
    }

    /**
     * relation for driver orders
     *
     * @return void
     */
    public function driverOrders()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    /**
     * relation for driver orders offers
     *
     * @return void
     */
    public function driverOffers()
    {
        return $this->hasMany(OrderOffer::class, 'driver_id');
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class);
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

    /**
     * coupons for family
     *
     * @return void
     */
    public function familyCoupons()
    {
        return $this->hasMany(Coupon::class, 'provider_id');
    }

    /**
     * relation for driver delivery type (now, later, now&later)
     *
     * @return void
     */
    public function deliveryType()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    /**
     * relation for regions
     *
     * @return void
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function histories()
    {
        return $this->hasMany(History::class);
    }
    

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'provider_id');
    }

    public function familySliders()
    {
        return $this->hasMany(FamilySlider::class, 'provider_id');
    }

    public function familySocials()
    {
        return $this->hasMany(FamilySocial::class, 'provider_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'provider_id');
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function periods()
    {
        return $this->hasMany(DriverTime::class);
    }

    public function userAddresses()
    {
        return $this->hasMany(UserAdresses::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}