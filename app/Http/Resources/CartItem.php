<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->product == null) {
            return [
                'key' => 'prodcut_deleted',
                'value' => 'this product not found in storage'
            ];
        } else {
            return [
                'cart_item_id'   =>(int)$this->id,
                'user_cart_id'   => $this->cart_id,
                'provider_id'    => $this->product == null ? 0 : (int)$this->product->provider->id,
                'product_id'     => $this->product == null ? 0 : (int)$this->product->id,
                'product_type'   => $this->product == null ? 0 : (int)$this->product->type_id,
                'product_name'   => $this->product == null ? 'تم حذف المنتج' : (string)$this->product->name,
                'quantity'       => (int)$this->quantity ,
                'total_price'    => (double)$this->total_price,
                'notes'          => (string)$this->notes,
                'additions'      => $this->additions == null ? [] : $this->getAdditions($this->additions) ,
                'more_additions' => $this->more_additions == null ? [] : $this->getAdditions($this->more_additions),
            ];
        }
    }
}
