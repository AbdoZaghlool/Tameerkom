<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Category extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'min_quantity' => (int)$this->min_quantity,
            'image' => (string)asset('uploads/categories/'.$this->image),
            'products' => Product::collection($this->whenLoaded('products')),
        ];
    }
}