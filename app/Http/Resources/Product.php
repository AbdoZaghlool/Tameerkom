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
        return [
            'id'               => (int)$this->id,
            'name'             => $this->name,
            'details'          => $this->details ?? 'لا يوجد',
            'price'            => (double)$this->price,
            // 'rate'             => $this->getRateValue(),
            'category_id'      => (int)$this->category_id,
            'category_name'    => (string)$this->category->name??'',
            'provider_id'      => (int)$this->provider_id,
            'provider_name'    => (string)$this->provider->name,
            'created_at'       => $this->created_at->format('Y-m-d H:i'),
            'images'           => Picture::collection($this->pictures)
        ];
    }
}