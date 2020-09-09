<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 19/4/20
 * Time: 10:59 PM
 */

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\BackendController;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderLineItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class OrderController extends BackendController
{
    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['sitetitle'] = 'Orders';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders                        = Order::whereDate('created_at', Carbon::today())->get();
        
        $this->data['total_order']     = $orders->count();
        $this->data['pending_order']   = $orders->where('status', OrderStatus::PENDING)->count();
        $this->data['process_order']   = $orders->where('status', OrderStatus::PROCESS)->count();
        $this->data['completed_order'] = $orders->where('status', OrderStatus::COMPLETED)->count();

        return view('admin.orders.index', $this->data);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $this->data['order'] = Order::findOrFail($id);
        if (!blank($this->data['order'])) {
            $this->data['items'] = OrderLineItem::where(['order_id' => $this->data['order']->id])->get();
        } else {
            $this->data['items'] = [];
        }
        return view('admin.orders.view', $this->data);
    }


        /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delivery($id)
    {
        $this->data['order'] = Order::where('delivery_boy_id', '!=', null)->findOrFail($id);
        if(blank($this->data['order']->delivery)) {
            return redirect(route('admin.orders.index'))->withError('The delivery boy not found');
        }
        return view('admin.orders.delivery', $this->data);
    }


    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $this->data['order'] = Order::findOrFail($id);
        if (!blank($this->data['order'])) {
            $this->data['items'] = OrderLineItem::where(['order_id' => $this->data['order']->id])->get();
        } else {
            $this->data['items'] = [];
        }
        return view('admin.orders.edit', $this->data);
    }

    /**
     * @param OrderRequest $request
     * @param $id
     *
     * @return mixed
     */
    public function update(OrderRequest $request, $id)
    {
        $orderHistory = OrderHistory::where(['order_id' => $id])->orderByRaw('id DESC')->first();
        if ($orderHistory) {
            if ($orderHistory->current_status != $request->status) {
                OrderHistory::create([
                    'order_id'        => $id,
                    'previous_status' => $orderHistory->current_status,
                    'current_status'  => $request->status,
                ]);
            }
        } else {
            $orderHistory = OrderHistory::create([
                'order_id'        => $id,
                'previous_status' => null,
                'current_status'  => OrderStatus::PENDING,
            ]);
            OrderHistory::create([
                'order_id'        => $id,
                'previous_status' => $orderHistory->current_status,
                'current_status'  => $request->status,
            ]);
        }

        $order         = Order::findOrFail($id);
        $order->status = $request->status;

        if ((int) $request->status === OrderStatus::COMPLETED) {
            $order->paid_amount    = $order->total;
            $order->payment_status = PaymentStatus::PAID;
        }

        $order->save();
        return redirect(route('admin.orders.index'))->withSuccess('The Data Updated Successfully');
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        Order::findOrFail($id)->delete();
        return redirect(route('admin.orders.index'))->withSuccess('The Data Deleted Successfully');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getOrder(Request $request)
    {
        if (request()->ajax()) {
            if (!empty($request->status)) {
                $startDate = $request->startDate;
                $endDate   = $request->endDate;
                $orders    = Order::where(['status' => $request->status])->where(function ($query) use (
                    $startDate,
                    $endDate
                ) {
                    if (!blank($startDate)) {
                        $startDate = Carbon::parse($startDate)->startOfDay()->toDateTimeString();
                        $endDate   = Carbon::parse(blank($endDate) ? $startDate : $endDate)->endOfDay()->toDateTimeString();
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    }

                })->orderBy('id', 'desc')->get();
            } else {
                $orders = Order::orderBy('id', 'desc')->get();
            }

            $i          = 1;
            $orderArray = [];
            if (!blank($orders)) {
                foreach ($orders as $order) {
                    $orderArray[$i]          = $order;
                    $orderArray[$i]['setID'] = $order->order_code;
                    $i++;
                }
            }

            return Datatables::of($orderArray)
                ->addColumn('action', function ($order) {
                    $retLink = '<a href="' . route('admin.orders.show',
                        $order) . '" class="btn btn-sm btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="far fa-eye"></i></a>&nbsp;&nbsp;&nbsp;<a href="' . route('admin.orders.edit',
                        $order) . '" class="pl-2 btn btn-sm btn-icon btn-primary"><i class="far fa-edit"></i></a>';
                    if($order->delivery_boy_id) {
                        $retLink .= '&nbsp;&nbsp;&nbsp;<a href="' . route('admin.orders.delivery', $order) . '" class="btn btn-sm btn-icon btn-success" data-toggle="tooltip" data-placement="top" title="Edit"><i class="far fa-list-alt"></i></a>';
                    }
                    return $retLink;
                })
                ->editColumn('user_id', function ($order) {
                    return (!blank($order->user) ? Str::limit($order->user->first_name . ' ' . $order->user->last_name,
                        20) : '');
                })
                ->editColumn('address', function ($order) {
                    return Str::limit($order->address, 30);
                })
                ->editColumn('created_at', function ($order) {
                    return Carbon::parse($order->created_at)->format('d M Y, h:i A');
                })
                ->editColumn('total', function ($order) {
                    return currencyFormat($order->total);
                })
                ->editColumn('status', function ($order) {
                    return trans('order_status.' . $order->status);
                })
                ->editColumn('id', function ($order) {
                    return $order->setID;
                })->make(true);
        }
    }
}
