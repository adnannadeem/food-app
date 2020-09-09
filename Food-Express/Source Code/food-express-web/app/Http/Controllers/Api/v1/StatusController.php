<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 24/4/20
 * Time: 1:30 PM
 */

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;

class StatusController extends Controller
{
    public function index($type, $flip = 0)
    {
        $returnArray = [];
        if ($type == 'order') {
            $returnArray = trans('order_status');
        }

        if ($type == 'status') {
            $returnArray = trans('statuses');
        }

        if ($type == 'currentstatus') {
            $returnArray = trans('current_statuses');
        }

        if ($type == 'product-receive') {
            $returnArray = trans('product_receive_status');
        }

        if ($type == 'delivery-history') {
            $returnArray = trans('delivery_history_status');
        }

        if (!blank($returnArray) && $flip == 0) {
            $i        = 0;
            $retArray = [];
            foreach ($returnArray as $key => $value) {
                $retArray[$i]['id']   = $key;
                $retArray[$i]['name'] = $value;
                $i++;
            }
            $returnArray = $retArray;
        }

        return response()->json([
            'status' => 200,
            'data'   => $returnArray,
        ]);
    }
}
