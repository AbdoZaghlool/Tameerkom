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
        $products = $this->whenLoaded('products');
        if ($request->products != null) {
            $products = $request->products;
        }
        return [
            'id'             => (int)$this->id,
            'name'           => $this->name,
            'region_id'      => (int)$this->city_id ? $this->city->region->id : 0,
            'region_name'    => $this->city ? (string)$this->city->region->name : 'no_region',
            'city_id'        => (int)$this->city_id,
            'city_name'      => $this->city ? (string)$this->city->name : 'no_city',
            'rate'           => $this->getRateValue(),
            'image'          => asset('uploads/users/'.$this->image),
            'prodcuts'       => Product::collection($products)
        ];
    }
}