<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 7/14/20
 * Time: 3:19 PM
 */

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopProductVariationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'unit_price'  => $this->price,
            'stock_count' => $this->stock_count,
            'in_stock'    => $this->in_stock,
        ];
    }

}
