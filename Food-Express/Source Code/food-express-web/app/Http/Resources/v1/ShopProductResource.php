<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ShopProductResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function toArray( $request )
    {
        if ( $this->resource instanceof Collection ) {
            return ShopProductResource::collection($this->resource);
        }

        return [
            'status' => 200,
            'data'   => [
                "id"       => $this->id,
                "name"     => $this->name,
                "image"    => $this->image(),
                'products' => ProductResource::collection(
                    $this->whenLoaded('products')
                ),
            ]
        ];
    }

    private function image()
    {
        if ( !blank($this->getMedia('shops')) ) {
            return asset($this->getFirstMediaUrl('shops'));
        }
        return asset('assets/img/default/shop.png');
    }
}
