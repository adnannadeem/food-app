<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ShopOrderRequest;
use App\Http\Resources\v1\ShopOrderItemResource;
use App\Http\Resources\v1\ShopOrderResource;
use App\Http\Resources\v1\UserResource;
use App\Libraries\MyString;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderLineItem;
use App\Models\Shop;
use App\Notifications\NewShopOrderCreated;
use App\Notifications\OrderCreated;
use App\Notifications\OrderUpdated;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ShopOrderController extends Controller
{
    /**
     * ShopOrderController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function search(Request $request)
    {
        $phone = $request->phone;
        $user  = User::where('phone', 'like', '%' . $phone . '%')->first();

        if (!blank($user)) {
            return response()->json([
                'status' => 200,
                'data'   => new UserResource($user),
            ]);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'The data not found',
        ]);
    }

    public function index()
    {
        $shop_id = auth()->user()->shop->id;

        $orders = Order::where(['shop_id' => $shop_id])->get();

        if (!blank($orders)) {
            return response()->json([
                'status' => 200,
                'data'   => ShopOrderResource::collection($orders),
            ]);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'The data not found',
        ]);
    }

    public function show($id)
    {
        $shop_id = auth()->user()->shop->id;
        $order   = Order::where(['shop_id' => $shop_id, 'id' => $id])->first();
        if (!blank($order)) {
            return response()->json([
                'status' => 200,
                'data'   => new ShopOrderItemResource($order),
            ]);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'The data not found',
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = new ShopOrderRequest(0);
        $validator = Validator::make($request->all(), $validator->rules());

        if (!$validator->fails()) {

            $phone = $request->phone;
            $user  = User::where('phone', 'like', '%' . $phone . '%')->first();

            if (blank($user)) {
                if ($request->get('email') != '') {
                    $getUser = User::where('email', $request->get('email'))->first();
                    if(!blank($getUser)) {
                        return response()->json([
                            'status'  => 422,
                            'message' => ['email' => ['The Email has already been taken.']]
                        ], 422);
                    }
                }

                $firstName = '';
                $lastName  = '';
                if ($request->has('name')) {
                    $parts     = $this->splitName($request->get('name'));
                    $firstName = $parts[0];
                    $lastName  = $parts[1];
                }

                $user             = new User;
                $user->first_name = $firstName;
                $user->last_name  = $lastName;
                $user->email      = $this->generateEmail($request->get('email'), $firstName);
                $user->phone      = $request->get('phone');
                $user->address    = $request->get('address');
                $user->roles      = UserRole::CUSTOMER;
                $user->username   = $this->username($user->email);
                $user->password   = Hash::make(123456);
                $user->save();
            }

            $order = [
                'user_id'         => $user->id,
                'shop_id'         => auth()->user()->shop->id,
                'total'           => $request->total,
                'sub_total'       => $request->total,
                'delivery_charge' => $request->delivery_charge,
                'status'          => OrderStatus::PENDING,
                'payment_status'  => PaymentStatus::UNPAID,
                'paid_amount'     => 0,
                'address'         => $request->delivery_address,
                'payment_method'  => PaymentMethod::CASH_ON_DELIVERY,
                'mobile'          => $request->customer_mobile,
                'lat'             => $request->customer_lat,
                'long'            => $request->customer_long,
                'misc'            => json_encode(["remarks" => $request->remarks]),
            ];

            $order          = Order::create($order);
            $orderId        = $order->id;
            $items          = json_decode($request->items);
            $orderLineItems = [];

            OrderHistory::create([
                'order_id'        => $orderId,
                'previous_status' => null,
                'current_status'  => OrderStatus::PENDING,
            ]);

            if ($items) {
                $i        = 0;
                $subTotal = 0;
                foreach ($items as $item) {
                    $itemTotal = (($item->quantity * $item->unit_price) - $item->discounted_price);
                    $subTotal += $itemTotal;
                    $orderLineItems['shop_id']          = $item->shop_id;
                    $orderLineItems['order_id']         = $orderId;
                    $orderLineItems['product_id']       = $item->product_id;
                    $orderLineItems['quantity']         = $item->quantity;
                    $orderLineItems['unit_price']       = $item->unit_price;
                    $orderLineItems['discounted_price'] = $item->discounted_price;
                    $orderLineItems['item_total']       = $itemTotal;
                    $i++;
                    OrderLineItem::create($orderLineItems);
                }
                $order            = Order::findOrFail($orderId);
                $order->sub_total = $subTotal;
                $order->save();
            }

            $order       = Order::findOrFail($orderId);
            $order->misc = json_encode([
                'order_code' => 'ORD-' . MyString::code($orderId),
                'remarks'    => $request->remarks,
            ]);
            $order->save();

            try {
                $request->user()->notify(new OrderCreated($order));
            } catch (\Exception $e) {
                // Using a generic exception

            }

            try {
                $order->shop->user->notify(new NewShopOrderCreated($order));
            } catch (\Exception $e) {
                // Using a generic exception

            }

            return response()->json([
                'status'  => 200,
                'message' => 'Successfully Created Order',
                'data'    => $this->createShow($orderId),
            ], 200);
        } else {
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors(),
            ], 422);
        }
    }

    private function createShow($id)
    {
        $response = Order::where(['id' => $id, 'shop_id' => auth()->user()->shop->id])
            ->orderBy('id', 'desc')
            ->with('items', 'invoice.transactions')->first();
        $response->setAttribute('status_name', trans('order_status.' . $response->status));
        $response->setAttribute('created_at_convert', $response->created_at->format('d M Y, h:i A'));
        $response->setAttribute('updated_at_convert', $response->updated_at->format('d M Y, h:i A'));

        if (isset($response['invoice'])) {
            $response['invoice']['created_at_convert'] = Carbon::parse($response['invoice']->created_at)->format('d M Y, h:i A');
            $response['invoice']['updated_at_convert'] = Carbon::parse($response['invoice']->updated_at)->format('d M Y, h:i A');
        }

        if (isset($response['invoice']) && isset($response['invoice']['transactions'])) {
            foreach ($response['invoice']['transactions'] as $transactionKey => $transaction) {
                $response['invoice']['transactions'][$transactionKey]['created_at_convert'] = Carbon::parse($transaction->created_at)->format('d M Y, h:i A');
                $response['invoice']['transactions'][$transactionKey]['updated_at_convert'] = Carbon::parse($transaction->updated_at)->format('d M Y, h:i A');
            }
        }

        if (isset($response['items'])) {
            foreach ($response['items'] as $itemKey => $item) {

                $response['items'][$itemKey]['created_at_convert'] = Carbon::parse($item->create_at)->format('d M Y, h:i A');
                $response['items'][$itemKey]['updated_at_convert'] = Carbon::parse($item->update_at)->format('d M Y, h:i A');
                $response['items'][$itemKey]['product']['image']   = $item['product']->images;
                unset($response['items'][$itemKey]['product']['media']);
            }
        }
        return $response;
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $getOrder = Order::where(['id' => $id, 'shop_id' => auth()->user()->shop->id])->first();
        if (!blank($getOrder)) {
            $validator = new ShopOrderRequest($id);
            $validator = Validator::make($request->all(), $validator->rules());
            if (!$validator->fails()) {
                $order = [];

                if (!blank($request->total)) {
                    $order['total'] = $request->total;
                }

                if (!blank($request->delivery_charge)) {
                    $order['delivery_charge'] = $request->delivery_charge;
                }

                if (!blank($request->status)) {
                    $order['status'] = $request->status;
                }

                if (!blank($request->payment_status)) {
                    $order['payment_status'] = $request->payment_status;
                }

                if (!blank($request->paid_amount)) {
                    $order['paid_amount'] = $request->paid_amount;
                }

                if (!blank($request->total)) {
                    $order['total'] = $request->total;
                }

                if (!blank($request->delivery_address)) {
                    $order['address'] = $request->delivery_address;
                }

                if (!blank($request->payment_method)) {
                    $order['payment_method'] = $request->payment_method;
                }

                if (!blank($request->customer_mobile)) {
                    $order['mobile'] = $request->customer_mobile;
                }

                if (!blank($request->customer_lat)) {
                    $order['lat'] = $request->customer_lat;
                }

                if (!blank($request->customer_long)) {
                    $order['long'] = $request->customer_long;
                }

                if (!blank($request->remarks)) {
                    $order['misc'] = json_encode([
                        "order_code" => $getOrder->order_code,
                        "remarks"    => $request->remarks,
                    ]);
                }

                Order::where(['id' => $id])->update($order);
                $orderId        = $id;
                $items          = (!blank($request->items) ? json_decode($request->items) : []);
                $orderLineItems = [];
                $orderHistory   = OrderHistory::where(['order_id' => $orderId])->orderByRaw('id DESC')->first();

                if ($orderHistory) {
                    if (!blank($request->status)) {
                        if ($orderHistory->current_status != $request->status) {
                            OrderHistory::create([
                                'order_id'        => $orderId,
                                'previous_status' => $orderHistory->current_status,
                                'current_status'  => $request->status,
                            ]);

                            try {
                                $request->user()->notify(new OrderUpdated($order));
                            } catch (\Exception $e) {
                                // Using a generic exception

                            }
                        }
                    }
                } else {
                    $orderHistory = OrderHistory::create([
                        'order_id'        => $orderId,
                        'previous_status' => null,
                        'current_status'  => OrderStatus::PENDING,
                    ]);
                    OrderHistory::create([
                        'order_id'        => $orderId,
                        'previous_status' => $orderHistory->current_status,
                        'current_status'  => $request->status,
                    ]);
                }

                if ($items) {
                    OrderLineItem::where(['order_id' => $orderId])->delete();
                    $i        = 0;
                    $subTotal = 0;
                    foreach ($items as $item) {
                        $itemTotal = (($item->quantity * $item->unit_price) - $item->discounted_price);
                        $subTotal += $itemTotal;
                        $orderLineItems['shop_id']          = $item->shop_id;
                        $orderLineItems['order_id']         = $orderId;
                        $orderLineItems['product_id']       = $item->product_id;
                        $orderLineItems['quantity']         = $item->quantity;
                        $orderLineItems['unit_price']       = $item->unit_price;
                        $orderLineItems['discounted_price'] = $item->discounted_price;
                        $orderLineItems['item_total']       = $itemTotal;
                        $i++;
                        OrderLineItem::create($orderLineItems);
                    }

                    $order            = Order::findOrFail($orderId);
                    $order->sub_total = $subTotal;
                    $order->save();
                }

                return response()->json([
                    'status'  => 200,
                    'message' => 'Successfully Updated Order',
                ], 200);
            } else {
                return response()->json([
                    'status'  => 422,
                    'message' => $validator->errors(),
                ], 422);
            }
        } else {
            return response()->json([
                'status'  => 400,
                'message' => 'Bad Request',
            ], 400);
        }
    }

    private function splitName($name)
    {
        $name       = trim($name);
        $last_name  = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . $last_name . '#', '', $name));
        return [$first_name, $last_name];
    }

    private function generateEmail($email = null, $name = null)
    {
        if ($email != '') {
            $user = User::where('email', $email)->first();
            if(!blank($user)) {
                return trim($name) . mt_rand() . '@' . $_SERVER['SERVER_NAME'];
            }
            return $email;
        }
        return trim($name) . mt_rand() . '@' . $_SERVER['SERVER_NAME'];
    }

    private function username($email)
    {
        $emails = explode('@', $email);
        return $emails[0] . mt_rand();
    }

}
