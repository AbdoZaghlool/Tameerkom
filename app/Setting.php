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
        'commission',
        'bank_name',
        'bank_number',
        'active_orders_count',
        'unpaid_commissions',
        'face_url',
        'twiter_url',
        'snapchat_url',
        'youtube_url',
        'insta_url',
    ];
}