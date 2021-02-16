<?php

namespace App;

use App\Http\Resources\Product;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    protected $guarded = [];

    protected $table = 'favourable';


    public function users()
    {
        return $this->morphedByMany(User::class, 'favourable', 'favourable');
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'favourable');
    }
}