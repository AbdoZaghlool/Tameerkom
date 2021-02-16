<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Value extends JsonResource
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
            'id'            => $this->id,
            'name'          => (string)$this->name,
            'property_id'   => (int)$this->property->id,
            'property_name' => (string)$this->property->name,
        ];
    }
}