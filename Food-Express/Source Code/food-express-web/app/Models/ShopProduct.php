<?php

namespace App\Models;


use Shipu\Watchable\Traits\WatchableTrait;

class ShopProduct extends BaseModel
{
    use WatchableTrait;

    protected $table       = 'shop_products';
    protected $auditColumn = true;

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productvariations()
    {
        return $this->hasMany(ShopProductVariation::class);
    }

    public function productoptions()
    {
        return $this->hasMany(ShopProductOption::class);
    }

    public function variations()
    {
        return $this->hasMany(ShopProductVariation::class);
    }

    public function options()
    {
        return $this->hasMany(ShopProductOption::class);
    }
    
}
