<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\DeliveryHistoryStatus;
use App\Enums\OrderStatus;
use App\Enums\ProductReceiveStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\DeliveryboyOrderResource;
use App\Http\Resources\v1\NotificationOrderResource;
use App\Http\Resources\v1\OrderApiResource;
use App\Models\DeliveryStatusHistories;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Enums\UserStatus;

class NotificationOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $order = [];
        if(auth()->user()->status == UserStatus::ACTIVE) {
            $order = Order::leftJoin('delivery_status_histories', function ($join) {
            $join->on('orders.id', '=', 'delivery_status_histories.order_id')->where('delivery_status_histories.user_id', '=', auth()->user()->id);
        })->where(['orders.delivery_boy_id' => null])->where('orders.status', '<=', OrderStatus::ACCEPT)->whereNULL('delivery_status_histories.id')->select('orders.*')->orderBy('orders.id', 'DESC')->get();
        }
        
        return response()->json([
            'status' => 200,
            'data'   => NotificationOrderResource::collection($order),
        ]);
    }

    public function orderAccept(Request $request, $id)
    {
        $order = Order::where(['id' => $id, 'delivery_boy_id' => null])->first();
        if (!blank($order)) {
            if (auth()->user()->roles == UserRole::DELIVERYBOY) {
                $validator = ['status' => ['required', 'numeric']];
                $validator = Validator::make($request->all(), $validator);
                if (!$validator->fails()) {
                    $message = 'Order cancel successful';
                    if ($request->status == DeliveryHistoryStatus::ACCEPT) {
                        $order->delivery_boy_id = auth()->user()->id;
                        $order->status          = OrderStatus::ON_THE_WAY;
                        $order->save();
                        $message = 'You got a new order';
                        $order   = Order::where(['id' => $id])->orderBy('id', 'desc')->with('items', 'invoice.transactions')->first();
                    }

                    if (blank(DeliveryStatusHistories::where([
                        'order_id' => $order->id,
                        'user_id'  => auth()->user()->id])->first())) {
                        DeliveryStatusHistories::create([
                            'order_id' => $order->id,
                            'user_id'  => auth()->user()->id,
                            'status'   => $request->status,
                        ]);
                    }

                    return response()->json([
                        'status'  => 200,
                        'message' => $message,
                        'data'    => new OrderApiResource($order),
                    ], 200);
                } else {
                    return response()->json([
                        'status'  => 422,
                        'message' => $validator->errors(),
                    ], 200);
                }
            } else {
                return response()->json([
                    'status'  => 400,
                    'message' => 'Bad user found',
                ], 400);
            }
        } else {
            return response()->json([
                'status'  => 400,
                'message' => 'Bad request',
            ], 400);
        }
    }

    public function OrderProductReceive(Request $request, $id)
    {
        $order = Order::where(['id' => $id, 'delivery_boy_id' => auth()->user()->id, 'product_received' => 10])->first();
        if (!blank($order)) {
            if (auth()->user()->roles == UserRole::DELIVERYBOY) {
                $validator = ['product_receive_status' => ['required', 'numeric']];
                $validator = Validator::make($request->all(), $validator);
                if (!$validator->fails()) {
                    $message = 'You could not receive the product.';
                    if ($request->product_receive_status == ProductReceiveStatus::RECEIVE) {
                        $order->product_received = $request->product_receive_status;
                        $order->save();
                        $message = 'You have received the product. carefully check your delivery product';
                        $order   = Order::where(['id' => $id])->orderBy('id', 'desc')->with('items', 'invoice.transactions')->first();

                        $orderHistory = OrderHistory::where(['order_id' => $id])->orderByRaw('id DESC')->first();
                        if ($orderHistory->current_status != OrderStatus::COMPLETED) {
                            OrderHistory::create([
                                'order_id'        => $id,
                                'previous_status' => $orderHistory->current_status,
                                'current_status'  => OrderStatus::ON_THE_WAY,
                            ]);
                        }
                    }

                    return response()->json([
                        'status'  => 200,
                        'message' => $message,
                        'data'    => new OrderApiResource($order),
                    ], 200);
                } else {
                    return response()->json([
                        'status'  => 422,
                        'message' => $validator->errors(),
                    ], 200);
                }
            } else {
                return response()->json([
                    'status'  => 400,
                    'message' => 'Bad user found',
                ], 400);
            }
        } else {
            return response()->json([
                'status'  => 400,
                'message' => 'Bad request',
            ], 400);
        }
    }

    public function orderStatus(Request $request, $id)
    {
        $order = Order::where(['id' => $id, 'delivery_boy_id' => auth()->user()->id])->first();
        if (!blank($order)) {
            if (auth()->user()->roles == UserRole::DELIVERYBOY) {
                $validator = ['status' => ['required', 'numeric']];
                $validator = Validator::make($request->all(), $validator);
                if (!$validator->fails()) {
                    $order->status = $request->status;
                    $order->save();

                    $orderHistory = OrderHistory::where(['order_id' => $id])->orderByRaw('id DESC')->first();
                    if ($orderHistory->current_status != OrderStatus::COMPLETED) {
                        OrderHistory::create([
                            'order_id'        => $id,
                            'previous_status' => $orderHistory->current_status,
                            'current_status'  => OrderStatus::COMPLETED,
                        ]);
                    }

                    return response()->json([
                        'status'  => 200,
                        'message' => 'Your process is complete',
                        'data'    => new OrderApiResource($order),
                    ], 200);
                } else {
                    return response()->json([
                        'status'  => 422,
                        'message' => $validator->errors(),
                    ], 200);
                }
            } else {
                return response()->json([
                    'status'  => 400,
                    'message' => 'Bad user found',
                ], 400);
            }
        } else {
            return response()->json([
                'status'  => 400,
                'message' => 'Bad request',
            ], 400);
        }
    }

    public function show($id)
    {
        $order   = Order::where(['id' => $id])->orderBy('id', 'desc')->with('items','items.product','items.variation', 'invoice.transactions')->first();

        return new OrderApiResource($order);
    }

    public function history()
    {
        $orders = Order::where(['delivery_boy_id' => auth()->user()->id])->orderBy('id', 'desc')->get();
        if (!blank($orders)) {
            return response()->json([
                'status' => 200,
                'data'   => DeliveryboyOrderResource::collection($orders),
            ]);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'The data not found',
        ]);
    }

}
