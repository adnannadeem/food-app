<?php

namespace App\Models;

use App\User;
use Shipu\Watchable\Traits\WatchableTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Shop extends BaseModel implements HasMedia
{
    use WatchableTrait, HasMediaTrait, HasSlug;
    protected $table       = 'shops';
    protected $guarded     = ['id'];
    protected $auditColumn = true;

    protected $fakeColumns = [];

    public function creator()
    {
        return $this->morphTo();
    }

    public function editor()
    {
        return $this->morphTo();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'shop_products');
    }

    public function shopproducts()
    {
        return $this->hasMany(ShopProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function getImagesAttribute()
    {
        if (!empty($this->getFirstMediaUrl('shops'))) {
            return asset($this->getFirstMediaUrl('shops'));
        }
        return asset('assets/img/default/shop.png');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }
}
