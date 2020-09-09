<?php

namespace App\Models;

use App\Models\BaseModel;
use Shipu\Watchable\Traits\WatchableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Location extends BaseModel
{
    use HasSlug;

    protected $auditColumn = true;
    protected $table       = 'locations';
    protected $fillable    = ['name', 'slug', 'status'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

}
