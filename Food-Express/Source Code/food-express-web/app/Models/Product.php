<?php

namespace App\Models;

use Shipu\Watchable\Traits\WatchableTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends BaseModel implements HasMedia
{
    use HasSlug, WatchableTrait, HasMediaTrait;

    protected $table       = 'products';
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

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_products');
    }

    public function orders()
    {
        return $this->hasMany(OrderLineItem::class);
    }

    public function scopeIsLive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * @param $query
     * @param Category $category
     * @return mixed
     */
    public function scopeFromCategory($query, Category $category)
    {
        return $query->whereHas('categories', function ($query) use ($category) {
            $query->where('category_id', $category->id);
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getImagesAttribute()
    {
        if (!empty($this->getFirstMediaUrl('products'))) {
            return asset($this->getFirstMediaUrl('products'));
        }
        return asset('assets/img/default/product.png');
    }

    public function getThumimagesAttribute()
    {
        $retArray = [];
        $products = $this->getMedia('products');
        if (!blank($products)) {
            foreach ($products as $key => $product) {
                $retArray[$key] = asset($product->getUrl());
            }
        }
        return $retArray;
    }

    public function inStock($shop_id)
    {
        return $this->stockCount($shop_id) > 0;
    }

    public function stockCount($shop_id)
    {
        return $this->shops()->where('shop_id', '=', $shop_id)->sum('quantity') - $this->orders()->where('shop_id', '=', $shop_id)->sum('quantity');
    }

    public function deleteMedia($product, $mediaName, $mediaId)
    {
        $media = Media::where([
            'file_name' => $mediaName,
            'collection_name' => 'products',
            'model_id' => $mediaId,
            'model_type' => Product::class,
        ])->first();

        if($media) {
            $media->delete();
        }

    }

}
