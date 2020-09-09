<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductIndexResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "description" => strip_tags($this->description),
            "unit_price" => $this->unit_price,
            "tags" => $this->tags,
            "order" => $this->order,
            "status"            => $this->status,
            'created_at'            => $this->created_at->format('d M Y, h:i A'),
            'updated_at'            => $this->updated_at->format('d M Y, h:i A'),
            'image'      => $this->image()
        ];
    }

    private function image()
    {
        if (!blank($this->getMedia('products'))) {
            return asset($this->getFirstMediaUrl('products'));
        }
        return asset( 'assets/img/default/product.png');
    }
}
