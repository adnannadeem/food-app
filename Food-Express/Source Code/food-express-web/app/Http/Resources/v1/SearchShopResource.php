<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchShopResource extends JsonResource
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
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'description'     => strip_tags($this->description),
            'delivery_charge' => $this->delivery_charge,
            'lat'             => $this->lat,
            'long'            => $this->long,
            'opening_time'    => $this->opening_time,
            'closing_time'    => $this->closing_time,
            'status'          => $this->status,
            'current_status'  => $this->current_status,
            'address'         => $this->address,
            'image'           => $this->image(),
            'location'        => $this->location($this->location),
            'area'            => $this->area($this->area),
        ];
    }

    private function image()
    {
        if ( !blank($this->getMedia('shops')) ) {
            return asset($this->getFirstMediaUrl('shops'));
        }
        return asset('assets/img/default/shop.png');
    }

    private function location( $location )
    {
        return [
            'name'   => $location->name,
            'slug'   => $location->slug,
            'status' => $location->status,
        ];
    }

    private function area( $area )
    {
        return [
            'name'   => $area->name,
            'slug'   => $area->slug,
            'status' => $area->status,
        ];
    }
}
