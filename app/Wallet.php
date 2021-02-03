<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable =
    [
        'user_id',
        'cash',
        'pull_request',
        'amount',
        'invoice_id',
        'payment_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
