<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FamilyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $products = $this->products;
        if ($request->search_type == 'product') {
            $products = $this->products->filter(function ($product) use ($request) {
                return (preg_match('%'.$request->keyword.'%', $product['name']) || preg_match('%'.$request->keyword.'%', $product['details']));
            });
        }

        return [
            'id'             => (int)$this->id,
            'name'           => $this->name,
            'brief'          => (string)$this->brief,
            'start_at'       => $this->work_start_at??'',
            'end_at'         => $this->work_end_at??'',
            'region_name'    => $this->region->name??'',
            'city_name'      => $this->city->name??'',
            'available'      => (int)$this->available,
            'rate'           => $this->getRateValue(),
            'orders_count'   => $this->familyOrders->count(),
            'porducts_count' => $this->products->count(),
            'distance'       => distanceBetweenTowPlaces($request->lat, $request->long, $this->latitude, $this->longitude),
            'image'          => asset('uploads/users/'.$this->image),
            'prodcuts'       => Product::collection($products)
        ];
    }
}
