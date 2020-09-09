<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 22/4/20
 * Time: 3:18 PM
 */

namespace App\Http\Resources\v1;


use Illuminate\Http\Resources\Json\JsonResource;

class ShopCategoryResource extends JsonResource
{
    public function toArray( $request )
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => strip_tags($this->description),
            'depth'       => $this->depth,
            'left'        => $this->left,
            'right'       => $this->right,
            'parent_id'   => $this->parent_id,
            'status'      => $this->status,
            'image'       => $this->image()
        ];
    }

    private function image()
    {
        if ( !blank($this->getMedia('categories')) ) {
            return asset($this->getFirstMediaUrl('categories'));
        }
        return asset('assets/img/default/category.png');
    }
}