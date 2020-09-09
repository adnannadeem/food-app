<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 17/4/20
 * Time: 11:47 AM
 */

namespace App\Http\Resources\v1;


use Illuminate\Http\Resources\Json\ResourceCollection;

class LocationResource extends ResourceCollection
{
    public function toArray( $request )
    {
        return [
            'status' => 200,
            'data'   => $this->collection,
        ];
    }
}