<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RequestProductResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function toArray($request)
    {
        if ($this->resource instanceof Collection) {
            return ProductResource::collection($this->resource);
        }

        $result = [
            'id'                  => $this->id,
            'name'                => $this->name,
            'description'         => strip_tags($this->description),
            'categories'          => Str::limit((implode(', ', $this->categories()->pluck('name')->toArray())), 30),
            'categoriesID'        => $this->categories()->pluck('id')->toArray(),
            'status'              => $this->status,
            'Requested'           => $this->requested,
            'create_date'         => $this->created_at->diffForHumans(),
            'unit_price'          => $this->unit_price,
            'product_order_count' => $this->orders->count(),
            'product_shop_count'  => $this->shops->count(),
            'image'               => $this->image(),
        ];

        return $result;
    }

    private function image()
    {
        if (!blank($this->getMedia('products'))) {
            return asset($this->getFirstMediaUrl('products'));
        }
        return asset('assets/img/default/product.png');
    }
}
