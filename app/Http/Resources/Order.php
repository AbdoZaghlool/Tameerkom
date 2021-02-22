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
            'provider_rate'        => $this->provider->getRateValue() ,
            'provider_image'       => asset('uploads/users/'.$this->provider->image) ,
            'user_name'            => $this->user->name ,
            'user_phone'           => $this->user->phone_number ,
            'price'                => (double)$this->price,
            'status'               => (string)$this->getStatus(),
            'recieve_place'        => (string)$this->recieve_place,
            'notes'                => (string)$this->notes,
            'created_at'           => $this->created_at->format('Y-m-d H:i') ?? '2020-10-25 15:20',
            'properties'           => $this->getAdditions($this->property_values) ,
        ];
    }
}