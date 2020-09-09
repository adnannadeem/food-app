<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ProductResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function toArray( $request )
    {
        if ( $this->resource instanceof Collection ) {
            return ProductResource::collection($this->resource);
        }

        $result = [
            'id'         => $this->id,
            'name'       => $this->name,
            'unit_price' => $this->unit_price,
            'image'      => $this->image(),
        ];


        if (isset($this->stock_count) && isset($this->in_stock)) {
            $result['stock_count']  = $this->stock_count;
            $result['in_stock']     = $this->in_stock;
        }

        if (isset($this->pivot->shop_id)) {
            $result['stock_count']  = $this->stockCount($this->pivot->shop_id);
            $result['in_stock']     = $this->inStock($this->pivot->shop_id);
        }

        return $result;
    }

    private function image()
    {
        if (!blank($this->getMedia('products'))) {
            return asset($this->getFirstMediaUrl('products'));
        }
        return asset( 'assets/img/default/product.png');
    }
}
