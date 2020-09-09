<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemsResource extends JsonResource
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
            'id'                => $this->id,
            'order_id'          => $this->order_id,
            'shop_id'           => $this->shop_id,
            'product_id'        => $this->product_id,
            'quantity'          => $this->quantity,
            'unit_price'        => $this->unit_price,
            'discounted_price'  => $this->discounted_price,
            'item_total'        => $this->item_total,
            'created_at'        => $this->created_at->format('d M Y, h:i A'),
            'updated_at'        => $this->updated_at->format('d M Y, h:i A'),
            'product'           => new ProductIndexResource($this->product),
            'variation'         => new ProductVariationResource($this->variation),
            'options'           => json_decode($this->options, true),
            'option_total'      => $this->options_total,
            'shop'              => new ShopResource($this->shop),
        ];
    }
}
