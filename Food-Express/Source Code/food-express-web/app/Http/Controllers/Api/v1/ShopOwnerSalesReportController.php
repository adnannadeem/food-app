<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 19/4/20
 * Time: 5:59 PM
 */

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ShopOwnerSalesReportResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopOwnerSalesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'nullable|date',
            'to_date'   => 'nullable|date|after_or_equal:from_date',
        ]);

        if (!$validator->fails()) {

            $queryArray['shop_id'] = auth()->user()->shop->id;
            $dateBetween = [];
            if ($request->from_date != '' && $request->to_date != '') {
                $dateBetween['from_date'] = date('Y-m-d', strtotime($request->from_date)) . ' 00:00:00';
                $dateBetween['to_date']   = date('Y-m-d', strtotime($request->to_date)) . ' 23:59:59';
            }

            if (!blank($dateBetween)) {
                $orders = Order::where($queryArray)->whereBetween('created_at', [$dateBetween['from_date'], $dateBetween['to_date']])->get();
            } else {
                $orders = Order::where($queryArray)->get();
            }

            return response()->json([
                'status' => 200,
                'data'   => ShopOwnerSalesReportResource::collection($orders),
            ]);

        } else {
            return response()->json([
                'status'  => 200,
                'message' => $validator->errors(),
            ], 200);
        }

    }
}
