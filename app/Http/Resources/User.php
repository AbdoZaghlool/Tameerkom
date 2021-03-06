<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'id'               => (int)$this->id,
            'phone_number'     => (string)$this->phone_number,
            'name'             => (string)$this->name,
            'type'             => (string)$this->getType(),
            'blocked'          => (bool)$this->blocked,
            'image'            => asset('uploads/users/'.$this->image),
            'email'            => (string)$this->email??'-',
            'api_token'        => (string)$this->api_token??'-',
            'latitude'         => $this->latitude ?? 0.1,
            'longitude'        => $this->longitude ?? 0.1,
            'commercial_record' => (string)$this->commercial_record,
            'commercial_image' => (string)asset('uploads/commercial_images/'.$this->commercial_image),
            'active'           => (int)$this->active,
            'region_id'        => (int)$this->city_id ? $this->city->region->id : 0,
            'region_name'      => $this->city ? (string)$this->city->region->name : 'no_region',
            'city_id'          => (int)$this->city_id,
            'city_name'        => $this->city ? (string)$this->city->name : 'no_city',
        ];
    }
}