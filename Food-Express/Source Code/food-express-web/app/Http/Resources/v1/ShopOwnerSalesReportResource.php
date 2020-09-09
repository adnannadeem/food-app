<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 18/4/20
 * Time: 2:07 PM
 */

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopOwnerSalesReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                  => $this->id,
            'order_code'          => $this->order_code,
            'shop_name'           => $this->shop->name,
            'total'               => $this->total,
            'sub_total'           => $this->sub_total,
            'delivery_charge'     => $this->delivery_charge,
            'platform'            => $this->platform,
            'device_id'           => $this->device_id,
            'ip'                  => $this->ip,
            'status'              => $this->status,
            'status_name'         => $this->GetOrderStatus,
            'payment_status'      => $this->payment_status,
            'payment_status_name' => $this->GetPaymentStatus,
            'payment_method'      => $this->payment_method,
            'payment_method_name' => $this->GetPaymentMethod,
            'paid_amount'         => $this->paid_amount,
            'address'             => $this->address,
            'invoice_id'          => $this->invoice_id,
            'deliver_boy_id'      => $this->deliver_boy_id,
            'delivery_boy_id'     => $this->delivery_boy_id,
            'shop_id'             => $this->shop_id,
            'product_received'    => $this->product_received,
            'mobile'              => $this->mobile,
            'lat'                 => $this->lat,
            'long'                => $this->long,
            'misc'                => $this->misc,
            'created_at'          => $this->created_at->format('d M Y, h:i A'),
            'updated_at'          => $this->updated_at->format('d M Y, h:i A'),
        ];
    }
}
