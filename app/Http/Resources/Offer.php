<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Offer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this);
        // return parent::toArray($request);
        return [
            "id"            => (int) $this->id,
            "driver_id"     => (int) $this->driver_id,
            "driver_name"   =>  $this->driver == null ? ' ' : $this->driver->name,
            "offer_details" => (string) $this->offer_details,
            "price"         => (double) $this->price,
            "status"        => $this->getStatus(),
            "order"         => array(new Order($this->order)),
        ];
    }
}
