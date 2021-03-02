<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Product extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $logedUser = \App\User::find($request->user_id);

        return [
            'id'                 => (int)$this->id,
            'name'               => $this->name,
            'details'            => $this->details ?? 'لا يوجد',
            'price'              => (double)$this->price,
            // 'rate'               => $this->getRateValue(),
            'category_id'        => (int)$this->category_id,
            'category_name'      => (string)$this->category->name??'',
            'provider_id'        => (int)$this->provider_id,
            'provider_name'      => (string)$this->provider->name,
            'provider_city'      => (string)$this->provider->city->name,
            'provider_image'     => (string)asset('uploads/users/'.$this->provider->image),
            'provider_blocked'   => (boolean)$this->provider->blocked,
            'provider_fav_state' => $logedUser == null ? false : ($logedUser->favouritUsers()->where('provider_id', $this->provider->id)->first() == null ? false : true),
            'created_at'         => $this->created_at->format('Y-m-d H:i'),
            'fav_state'          => $logedUser == null ? false : ($logedUser->favouritProducts()->where('product_id', $this->id)->first() == null ? false : true),
            'property_values'    => Value::collection($this->values),
            'images'             => Picture::collection($this->pictures)
        ];
    }
}