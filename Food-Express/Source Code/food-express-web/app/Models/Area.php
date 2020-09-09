<?php

namespace App\Models;

use Shipu\Watchable\Traits\WatchableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Area extends BaseModel
{
    use HasSlug, WatchableTrait;

    protected $table       = 'areas';
    protected $auditColumn = true;
    protected $fillable    = ['name', 'slug', 'location_id', 'status'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
