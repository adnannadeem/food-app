<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderStoreRequest;
use App\Http\Resources\v1\OrderApiResource;
use App\Http\Resources\v1\OrderResource;
use App\Libraries\MyString;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderLineItem;
use App\Models\ShopProductVariation;
use App\Notifications\NewShopOrderCreated;
use App\Notifications\OrderCreated;
use App\Notifications\OrderUpdated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @return OrderResource
     */
    public function index()
    {
        $response = Order::where(['user_id' => auth()->user()->id])->orderBy('id', 'desc')->with('items')->get();
        $response->map(function ($post) {
            $post['status_name']        = trans('order_status.' . $post->status);
            $post['created_at_convert'] = Carbon::parse($post->create_at)->format('d M Y, h:i A');
            $post['updated_at_convert'] = Carbon::parse($post->update_at)->format('d M Y, h:i A');

            foreach ($post['items'] as $itemKey => $item) {
                $post['items'][$itemKey]['created_at_convert'] = Carbon::parse($post->create_at)->format('d M Y, h:i A');
                $post['items'][$itemKey]['updated_at_convert'] = Carbon::parse($post->update_at)->format('d M Y, h:i A');
                $post['items'][$itemKey]['product']['image']   = $item['product']->images;
            }
            return $post;
        });

        return new OrderResource($response);
    }

    public function show($id)
    {
        $response = Order::where(['id' => $id, 'user_id' => auth()->user()->id])
            ->orderBy('id', 'desc')
            ->with('items', 'invoice.transactions')->first();
        return new OrderApiResource($response);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = new OrderStoreRequest();
        $validator = Validator::make($request->all(), $validator->rules());

        if ($request->get('image') != '') {
            $validType      = ['jpg', 'png', 'jpeg', 'pdf', 'docx'];
            $fileName       = $request->get('fileName');
            $validExtension = false;
            if (empty($fileName)) {
                $validExtension = true;
            } else {
                $extension = explode('.', $fileName);
                $extension = strtolower(end($extension));

                if (!in_array($extension, $validType)) {
                    $validExtension = true;
                }
            }

            $validator->after(function ($validator) use ($validExtension) {
                if ($validExtension) {
                    $validator->errors()->add('image', 'This image type was invalid.');
                }
            });
        }

        if (!$validator->fails()) {
            $order = [
                'user_id'         => auth()->user()->id,
                'shop_id'         => (int) $request->get('shop_id'),
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

            $order   = Order::create($order);
            $orderId = $order->id;
            $items   = json_decode($request->items);

            if ($request->get('image') != '') {

                $realImage = base64_decode($request->get('image'));
                file_put_contents($request->get('fileName'), $realImage);

                $url = public_path($request->get('fileName'));

                $order->media()->delete();
                $order->addMediaFromUrl($url)->toMediaCollection('order');

                File::delete($url);
            }

            OrderHistory::create([
                'order_id'        => $orderId,
                'previous_status' => null,
                'current_status'  => OrderStatus::PENDING,
            ]);

            $orderLineItems = [];
            if (!blank($items)) {
                $i        = 0;
                $subTotal = 0;
                foreach ($items as $item) {

                    $optionsArray['options']   = isset($item->options) ? $item->options : [];
                    $optionsArray['variation'] = [];
                    if ((int) $item->shop_product_variation_id) {
                        $variation = ShopProductVariation::find($item->shop_product_variation_id);

                        if (!blank($variation)) {
                            $optionsArray['variation'] = ['id' => $variation->id, 'name' => $variation->name, 'price' => $variation->price];
                        }
                    }

                    $optionTotal = 0;
                    if (isset($item->options) && !blank($item->options)) {
                        foreach ($item->options as $option) {
                            $optionTotal += $option->price;
                        }
                    }

                    $itemTotal = (($item->quantity * ($item->unit_price + $optionTotal)) - $item->discounted_price);
                    $subTotal += $itemTotal;

                    $orderLineItems['shop_id']                   = $item->shop_id;
                    $orderLineItems['order_id']                  = $orderId;
                    $orderLineItems['product_id']                = $item->product_id;
                    $orderLineItems['quantity']                  = $item->quantity;
                    $orderLineItems['unit_price']                = $item->unit_price;
                    $orderLineItems['discounted_price']          = $item->discounted_price;
                    $orderLineItems['item_total']                = $itemTotal;
                    $orderLineItems['shop_product_variation_id'] = $item->shop_product_variation_id;
                    $orderLineItems['options']                   = json_encode($optionsArray);
                    $orderLineItems['options_total']             = $optionTotal;
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

            $request->user()->notify(new OrderCreated($order));

            $order->shop->user->notify(new NewShopOrderCreated($order));

            return response()->json([
                'status'  => 200,
                'message' => 'Successfully Created Order',
                'data'    => $this->createShow($orderId),
            ], 200);
        } else {
            return response()->json([
                'status'  => 200,
                'message' => $validator->errors(),
            ], 200);
        }
    }

    private function createShow($id)
    {
        $response = Order::where(['id' => $id, 'user_id' => auth()->user()->id])
            ->orderBy('id', 'desc')
            ->with('items', 'invoice.transactions')->first();
        $response->setAttribute('status_name', trans('order_status.' . $response->status));
        $response->setAttribute('created_at_convert', $response->created_at->format('d M Y, h:i A'));
        $response->setAttribute('updated_at_convert', $response->updated_at->format('d M Y, h:i A'));
        $response->setAttribute('attachment', $response->image);

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
                $response['items'][$itemKey]['options']            = json_decode($item->options);
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
        $getOrder = Order::where(['id' => $id, 'user_id' => auth()->user()->id])->first();
        if (!blank($getOrder)) {
            $validator = new OrderStoreRequest($id);
            $validator = Validator::make($request->all(), $validator->rules());

            if ($request->get('image') != '') {
                $validType      = ['jpg', 'png', 'jpeg', 'pdf', 'docx'];
                $fileName       = $request->get('fileName');
                $validExtension = false;
                if (empty($fileName)) {
                    $validExtension = true;
                } else {
                    $extension = explode('.', $fileName);
                    $extension = strtolower(end($extension));

                    if (!in_array($extension, $validType)) {
                        $validExtension = true;
                    }
                }

                $validator->after(function ($validator) use ($validExtension) {
                    if ($validExtension) {
                        $validator->errors()->add('image', 'This image type was invalid.');
                    }
                });
            }

            if (!$validator->fails()) {
                $order = [
                    'user_id' => auth()->user()->id,
                ];

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
                $orderId      = $id;
                $items        = (!blank($request->items) ? json_decode($request->items) : []);
                $orderHistory = OrderHistory::where(['order_id' => $orderId])->orderByRaw('id DESC')->first();

                if ($request->get('image') != '') {

                    $realImage = base64_decode($request->get('image'));
                    file_put_contents($request->get('fileName'), $realImage);

                    $url = public_path($request->get('fileName'));

                    $getOrder->media()->delete();
                    $getOrder->addMediaFromUrl($url)->toMediaCollection('products');

                    File::delete($url);
                }

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

                $orderLineItems = [];
                if ($items) {
                    OrderLineItem::where(['order_id' => $orderId])->delete();
                    $i        = 0;
                    $subTotal = 0;
                    foreach ($items as $item) {

                        $optionsArray['options']   = isset($item->options) ? $item->options : [];
                        $optionsArray['variation'] = [];
                        if ((int) $item->shop_product_variation_id) {
                            $variation = ShopProductVariation::find($item->shop_product_variation_id);

                            if (!blank($variation)) {
                                $optionsArray['variation'] = ['id' => $variation->id, 'name' => $variation->name, 'price' => $variation->price];
                            }
                        }

                        $optionTotal = 0;
                        if (!blank($item->options)) {
                            foreach ($item->options as $option) {
                                $optionTotal += $option->price;
                            }
                        }

                        $itemTotal = (($item->quantity * ($item->unit_price + $optionTotal)) - $item->discounted_price);
                        $subTotal += $itemTotal;

                        $orderLineItems['shop_id']                   = $item->shop_id;
                        $orderLineItems['order_id']                  = $orderId;
                        $orderLineItems['product_id']                = $item->product_id;
                        $orderLineItems['quantity']                  = $item->quantity;
                        $orderLineItems['unit_price']                = $item->unit_price;
                        $orderLineItems['discounted_price']          = $item->discounted_price;
                        $orderLineItems['item_total']                = $itemTotal;
                        $orderLineItems['shop_product_variation_id'] = $item->shop_product_variation_id;
                        $orderLineItems['options']                   = json_encode($optionsArray);
                        $orderLineItems['options_total']             = $optionTotal;
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
                    'status'  => 200,
                    'message' => $validator->errors(),
                ], 200);
            }
        } else {
            return response()->json([
                'status'  => 400,
                'message' => 'Bad Request',
            ], 400);
        }
    }

    public function orderPayment(Request $request)
    {
        $order_id       = $request->order_id;
        $payment_method = $request->payment_method;

        //$amount                 = $request->amount;
        //$user_id                = $request->user_id;
        //payment_transaction_id = $request->payment_transaction_id;

        $payment  = new \App\Http\Services\CheckoutService($order_id, $payment_method);
        $response = $payment->payment();
        if ($response['status']) {
            return response()->json([
                'status'  => true,
                'data'    => '',
                'message' => $response['message'],
            ], 200);
        }
        return response()->json([
            'status'  => false,
            'data'    => '',
            'message' => $response['message'],
        ], 200);
    }

    public function attachment($id)
    {
        $getOrder = Order::where(['id' => $id, 'user_id' => auth()->user()->id])->first();
        if (!blank($getOrder)) {
            return response()->json([
                'data'    => $getOrder->image,
                'status'  => 200,
                'message' => 'Success',
            ], 200);
        }
        return response()->json([
            'status'  => 400,
            'message' => 'Bad Request',
        ], 400);
    }
}
