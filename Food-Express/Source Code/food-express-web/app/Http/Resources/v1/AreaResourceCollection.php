<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 22/4/20
 * Time: 4:55 PM
 */

namespace App\Http\Resources\v1;


use Illuminate\Http\Resources\Json\ResourceCollection;

class AreaResourceCollection extends ResourceCollection
{
    public function toArray( $request )
    {
        return [
            'status' => 200,
            'data'   => $this->collection,
        ];
    }
}