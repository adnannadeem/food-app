<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchShopProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray( $request )
    {
        $result = [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => strip_tags($this->description),
            'unit_price'  => $this->unit_price,
            'tags'        => $this->tags,
            'order'       => $this->order,
            'status'      => $this->status,
            'image'       => $this->image(),
        ];

        if (isset($this->stock_count) && isset($this->in_stock)) {
            $result['stock_count']  = $this->stock_count;
            $result['in_stock']     = $this->in_stock;
        }
        return $result;
    }

    private function image()
    {
        if ( !blank($this->getMedia('products')) ) {
            return asset($this->getFirstMediaUrl('products'));
        }
        return asset('assets/img/default/product.png');
    }
}
