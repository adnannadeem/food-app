<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductVariation extends Model
{
    public $timestamps = false;
    protected $fillable = ['shop_product_id', 'product_id', 'shop_id', 'name', 'price', 'quantity'];
}
