<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    protected $fillable = [
        'email',
        'phone',
        'logo',
        'family_commission',
        'driver_commission',
        'distance',
        'fatoora_com',
        'face_url',
        'twiter_url',
        'snapchat_url',
        'youtube_url',
        'insta_url',
        'version',
        'product_limit',
        'delivery_time',
        'family_offer_time',
        'order_payment_time',
        'accept_order_time',
        'min_value_withdrow_family',
        'min_value_withdrow_driver',
        'tax',
        'scheduled_order_duration',
    ];
}
