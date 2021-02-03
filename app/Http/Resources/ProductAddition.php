<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductAddition extends JsonResource
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
            'id'    => (int)$this->id,
            'name'  => $this->name,
            'price' => (double)$this->price,
            // 'type' => $this->type == 0 ? 'main' : '',
        ];
    }
}
