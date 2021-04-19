<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Provider extends JsonResource
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
            'name'           => $this->name,
            'region_id'      => (int)@$this->city->region->id,
            'region_name'    => (string)@$this->city->region->name,
            'city_id'        => (int)$this->city_id,
            'city_name'      => (string)@$this->city->name,
            'blocked'        => (bool)$this->blocked,
            'rate'           => $this->getRateValue(),
            'image'          => asset('uploads/users/'.$this->image),
            'prodcuts'       => Product::collection($this->products)
        ];
    }
}