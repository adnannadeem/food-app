<?php

namespace App\Http\Resources\v1;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            "id"              => $this->id,
            "name"            => $this->name,
            "user_id"         => $this->user_id,
            "location_id"     => $this->location_id,
            "area_id"         => $this->area_id,
            "description"     => strip_tags($this->description),
            "delivery_charge" => $this->delivery_charge,
            "lat"             => $this->lat,
            "long"            => $this->long,
            "opening_time"    => Carbon::parse($this->opening_time)->format('h:i A'),
            "closing_time"    => Carbon::parse($this->closing_time)->format('h:i A'),
            "address"         => $this->address,
            "status"          => trans('shop_status.' . $this->status),
            "current_status"  => trans('current_status.' . $this->current_status),
            "applied"         => $this->applied,
            'created_at'      => $this->created_at->format('d M Y, h:i A'),
            'updated_at'      => $this->updated_at->format('d M Y, h:i A'),
            "image"           => $this->image(),
        ];
    }

    private function image()
    {
        if (!blank($this->getMedia('shops'))) {
            return asset($this->getFirstMediaUrl('shops'));
        }
        return asset('assets/img/default/shop.png');
    }

}
