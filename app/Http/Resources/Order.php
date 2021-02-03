<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id'                   => (int)$this->id,
            'provider_id'          => (int)$this->provider->id ,
            'provider_name'        => $this->provider->name ,
            'provider_phone'       => $this->provider->phone_number ,
            'provider_lat'         => $this->provider->latitude ,
            'provider_long'        => $this->provider->longitude ,
            'provider_rate'        => $this->provider->getRateValue() ,
            'provider_image'       => asset('uploads/users/'.$this->provider->image) ,
            'user_name'            => $this->user->name ,
            'user_phone'           => $this->user->phone_number ,
            'user_rate'            => $this->user->getRateValue() ,
            'user_address_title'   => (string)$this->address->title ,
            'user_address_lat'     => (double)$this->address->latitude ,
            'user_address_long'    => (double)$this->address->longitude ,
            'driver_id'            => (int)$this->driver_id ?? 0 ,
            'driver_name'          => (string)$this->driver_id == null ?'': $this->driver->name ,
            'driver_phone'         => (string)$this->driver_id == null ?'': $this->driver->phone_number ,
            'driver_image'         => (string)$this->driver_id == null ?'': asset('uploads/users/'.$this->driver->image) ,
            'price'                => (double)$this->price,
            'coupon_name'          => (string)$this->coupon == null ? '' :$this->coupon->name,
            'delivery_price'       => (double)$this->driver_id == null ? 0.1 : (($this->offers()->where('driver_id', $this->driver_id)->first()) == null ? 0.1 :$this->offers()->where('driver_id', $this->driver_id)->first()->price),
            'user_status'          => (string)$this->getStatus(),
            'provider_status'      => (string)$this->getProviderStatus(),
            'type'                 => $this->type_id == null ? '':$this->orderType->type,
            'recieve_at'           => (string)$this->recieve_at,
            'notes'                => $this->notes ?? '-',
            // 'user_family_distance' => distanceBetweenTowPlaces($this->provider->latitude, $this->provider->longitude, $this->address->latitude??0, $this->address->latitude??0),
            'created_at'           => $this->created_at->format('Y-m-d H:i') ?? '2020-10-25 15:20',
            'cart_items'           => CartItem::collection(unserialize($this->cart_items)) ,
        ];
    }
}