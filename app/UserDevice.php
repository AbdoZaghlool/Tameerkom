<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    //
    protected $table = 'user_devices';
    protected $fillable = [
        'user_id', 'device_type', 'device_token'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }
}
