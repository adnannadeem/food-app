<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 19/4/20
 * Time: 5:59 PM
 */

namespace App\Http\Controllers\Api\v1;


use App\Enums\ShopStatus;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\AreaResourceCollection;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * @param Request $request
     * @return AreaResourceCollection
     */
    public function index( Request $request )
    {
        if($request->has('id')) {
            $response = Shop::where([ 'area_id' => $request->get('id'), 'status' => ShopStatus::ACTIVE ])->get();
        } elseif($request->headers->has('X-FOOD-LAT') && $request->headers->has('X-FOOD-LONG')) {
            $response = Shop::where(['status' => ShopStatus::ACTIVE ])
                ->select(DB::raw('*, ( 6367 * acos( cos( radians('.$request->headers->get('X-FOOD-LAT').') ) * cos( radians( `lat` ) ) * cos( radians( `long` ) - radians('.$request->headers->get('X-FOOD-LONG').') ) + sin( radians('.$request->headers->get('X-FOOD-LAT').') ) * sin( radians( `lat` ) ) ) ) AS distance'))
                ->having('distance', '<', setting('geolocation_distance_radius'))
                ->orderBy('distance')
                ->get();
        } else {
            $response = Shop::where(['status' => ShopStatus::ACTIVE ])->get();
        }

        return new AreaResourceCollection($response);
    }
}
