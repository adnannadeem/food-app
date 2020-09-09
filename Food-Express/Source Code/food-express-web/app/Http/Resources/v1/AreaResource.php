<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
{
    public function toArray( $request )
    {
        return [

            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'location_id'     => $this->location_id,
            'area_id'         => $this->area_id,
            'name'            => $this->name,
            'description'     => strip_tags($this->description),
            'delivery_charge' => $this->delivery_charge,
            'lat'             => $this->lat,
            'long'            => $this->long,
            'opening_time'    => $this->opening_time,
            'closing_time'    => $this->closing_time,
            'address'         => $this->address,
            'status'          => $this->status,
            'current_status'  => $this->current_status,
            'creator_type'    => $this->creator_type,
            'creator_id'      => $this->creator_id,
            'editor_type'     => $this->editor_type,
            'editor_id'       => $this->editor_id,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
            'image'           => $this->image()
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
