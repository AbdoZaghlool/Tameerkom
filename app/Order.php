<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'provider_id',
        'user_id',
        'product_id',
        'price',
        'property_values',
        'count',
        'status', // 0 => active , 1 => compeleted , 2 => canceled, 3 => waitingConfirmation
        'recieve_place',
        'payment_image',
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
        $data = unserialize($json);
        $arr = [];
        if (count($data) > 0) {
            foreach ($data as $addition) {
                $add = PropertyValue::with('property')->find($addition);
                if ($add) {
                    array_push($arr, [
                        'id'    => (int)$add->id,
                        'name'  => (string)$add->name,
                        'property_id' => (int)$add->property->id,
                        'property_name' => (string)$add->property->name,
                    ]);
                }
            }
        }
        return $arr;
    }

    public function getStatus()
    {
        $status = $this->status == '0' ? 'نشط' : ($this->status == '1' ? 'مكتمل' : ($this->status == '2' ? 'ملغي' : 'في انتظار التاكيد'));
        return $status;
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
}