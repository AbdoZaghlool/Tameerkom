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
            'brief'             => (string)$this->brief,
            'type'             => (string)$this->getType(),
            'image'            => asset('uploads/users/'.$this->image),
            'email'            => (string)$this->email??'-',
            'api_token'        => (string)$this->api_token??'-',
            'latitude'         => (double)$this->latitude,
            'longitude'        => (double)$this->longitude,
            'tax_number'       => (string)$this->tax_number,
            'work_start_at'    => (string)$this->work_start_at,
            'work_end_at'      => (string)$this->work_end_at,
            'available'        => (int)$this->available,
            'active'           => (int)$this->active,
            'region_id'        => (int)$this->region_id,
            'region_name'      => $this->region ? (string)$this->region->name : 'no_region',
            'city_id'          => (int)$this->city_id,
            'city_name'        => $this->city ? (string)$this->city->name : 'no_city',
            'bank_user_name'   => (string)$this->bank_user_name,
            'bank_name'        => (string)$this->bank_name,
            'account_number'   => (string)$this->account_number,
            'insurance_number' => (string)$this->insurance_number,
            'identity_number'  => (string)$this->identity_number,
            'driver_license'   => (string)$this->driver_license,
            'type_id'          => (int)$this->type_id,
            'car_license'      => $this->car_license == null? "no_image":asset('uploads/cars/'.$this->car_license),
            'family_topics'    => $this->topics()->pluck('topic_id')->toArray(),
        ];
    }
}
