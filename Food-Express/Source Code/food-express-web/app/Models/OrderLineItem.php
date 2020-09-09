<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLineItem extends Model
{
    protected $table    = 'order_line_items';
    protected $fillable = ['shop_id', 'order_id', 'product_id', 'quantity', 'unit_price', 'discounted_price', 'item_total', 'shop_product_variation_id', 'options', 'options_total'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ShopProductVariation::class, 'shop_product_variation_id');
    }
}
