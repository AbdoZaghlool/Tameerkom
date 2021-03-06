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
        return [
            'id'             => (int)$this->id,
            'provider_id'    => (int)$this->provider->id ,
            'provider_name'  => $this->provider->name ,
            'provider_phone' => $this->provider->phone_number ,
            'provider_rate'  => 3.5 ,
            'provider_image' => asset('uploads/users/'.$this->provider->image) ,
            'user_id'        => $this->user_id ,
            'user_name'      => $this->user->name ,
            'user_phone'     => $this->user->phone_number ,
            'price'          => (double)$this->price,
            'tax'            => (int)ceil($this->tax),
            'count'          => (int)$this->count,
            'status'         => (string)$this->getStatus(),
            'payment_image'  => $this->payment_image == null ? '' : asset('uploads/payment_images/'.$this->payment_image),
            'payment_status' => (int)$this->payment_status,
            'recieve_place'  => (string)$this->recieve_place,
            'notes'          => (string)$this->notes,
            'created_at'     => $this->created_at->format('Y-m-d H:i') ?? '2020-10-25 15:20',
            'product'        => new Product($this->product),
        ];
    }
}