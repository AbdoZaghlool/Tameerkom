<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'provider_id',
        'user_id',
        'driver_id',
        'cart_items',
        'price',
        'status', // 0 => new , 1 => accepted , 2 => active, 3 => done
        'provider_status', // 0 => new , 1 => active , 2 => compeleted,
        'type_id', // in [1,2] now or later.
        'recieve_at',
        'coupon_id',
        'user_adresses_id',
        'invoice_id',
        'delivery_type', // delivery from driver or user recieve from family or outer company.
        'payment_status',
        'notes',
        'tax',
    ];


    /**
     * get json codded additions from database
     *
     * @param ProductAddition $json
     * @return array $productAdditons
     */
    public function getAdditions($json)
    {
        $data = json_decode($json);
        $arr = [];
        if (count($data) > 0) {
            foreach ($data as $addition) {
                $add = ProductAddition::find($addition);
                array_push($arr, [
                    'id'    => (int)$add->id,
                    'name'  => (string)$add->name,
                    'price' => (double)$add->price,
                ]);
            }
        }
        return $arr;
    }

    public function getStatus()
    {
        $status = $this->status == '0' ? 'جديد' : ($this->status == '1' ? 'مقبول' : ($this->status == '2' ? 'نشط' : ($this->status == '3' ? 'منتهي' : ($this->status == '4' ? 'ملغي' : ''))));
        return $status;
    }

    public function getProviderStatus()
    {
        $status = $this->provider_status == '0' ? 'جديد' : ($this->provider_status == '1' ? 'نشط' : ($this->provider_status == '2' ? 'منتهي' : ($this->provider_status == '3' ? 'ملغي' : ($this->provider_status == '9' ? 'معلق' : ''))));
        return $status;
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAdresses::class, 'user_adresses_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderType()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function offers()
    {
        return $this->hasMany(OrderOffer::class);
    }

    /**
     * filter the model by status
     *
     * @param QueryBiulder $query
     * @param Request $params
     * @return QueryBuilder $query
     */
    public function scopeStatus($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('status', $params);
        }
        return $query;
    }

    /**
     * filter the model by provider_status
     *
     * @param QueryBiulder $query
     * @param Request $params
     * @return QuriyBiulder $query
     */
    public function scopeProviderStatus($query, $params)
    {
        if (isset($params) && trim($params !== '')) {
            $query->where('provider_status', $params);
        }
        return $query;
    }
}
