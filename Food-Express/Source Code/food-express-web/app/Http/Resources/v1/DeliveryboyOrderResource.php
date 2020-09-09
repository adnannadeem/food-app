<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 18/4/20
 * Time: 2:07 PM
 */

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryboyOrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'total'            => $this->total,
            'sub_total'        => $this->sub_total,
            'delivery_charge'  => $this->delivery_charge,
            'status'           => $this->status,
            'status_name'      => trans('order_status.' . $this->status),
            'platform'         => $this->platform,
            'device_id'        => $this->device_id,
            'ip'               => $this->ip,
            'payment_status'   => $this->payment_status,
            'paid_amount'      => $this->paid_amount,
            'address'          => $this->address,
            'mobile'           => $this->mobile,
            'lat'              => $this->lat,
            'long'             => $this->long,
            'misc'             => $this->misc,
            'payment_method'   => $this->payment_method,
            'invoice_id'       => $this->invoice_id,
            'delivery_boy_id'  => $this->delivery_boy_id,
            'shop_id'          => $this->shop_id,
            'product_received' => $this->product_received,
            'created_at'       => $this->created_at->format('d M Y, h:i A'),
            'updated_at'       => $this->updated_at->format('d M Y, h:i A'),
            'user'             => new UserResource($this->user),
            'shop'             => new ShopResource($this->shop),
        ];
    }
}
