<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserApplied;
use App\Enums\UserRole;
use App\Http\Controllers\BackendController;
use App\Http\Requests\DeliveryBoyRequest;
use App\Models\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

class DeliveryBoyController extends BackendController
{
    public function __construct()
    {
        $this->data['sitetitle'] = 'Delivery Boys';
        $this->data['userRole']  = UserRole::DELIVERYBOY;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.deliveryboy.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.deliveryboy.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeliveryBoyRequest $request)
    {
        $user             = new User;
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->username   = $request->username ?? $this->username($request->email);
        $user->password   = Hash::make(request('password'));
        $user->phone      = $request->phone;
        $user->address    = $request->address;
        $user->roles      = $request->roles;
        $user->status     = $request->status;
        $user->applied    = UserApplied::ADMIN;
        $user->save();

        if (request()->file('image')) {
            $user->addMedia(request()->file('image'))->toMediaCollection('user');
        }

        return redirect(route('admin.deliveryboys.index'))->withSuccess('The Data Inserted Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->data['user'] = User::where('roles', UserRole::DELIVERYBOY)->findOrFail($id);
        return view('admin.deliveryboy.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['user'] = User::where('roles', UserRole::DELIVERYBOY)->findOrFail($id);
        return view('admin.deliveryboy.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DeliveryBoyRequest $request, $id)
    {
        $user             = User::where('roles', UserRole::DELIVERYBOY)->findOrFail($id);
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->username   = $request->username ?? $this->username($request->email);

        if ($request->password) {
            $user->password = Hash::make(request('password'));
        }

        $user->phone   = $request->phone;
        $user->address = $request->address;
        $user->roles   = $request->roles;
        $user->status  = $request->status;
        $user->save();

        if (request()->file('image')) {
            $user->media()->delete();
            $user->addMedia(request()->file('image'))->toMediaCollection('user');
        }

        return redirect(route('admin.deliveryboys.index'))->withSuccess('The Data Updated Successfully');
    }

    public function getDeliveryBoy()
    {
        $users     = User::where('roles', UserRole::DELIVERYBOY)->orderBy('id', 'desc')->get();
        $userArray = [];

        $i = 1;
        if (!blank($users)) {
            foreach ($users as $user) {
                $userArray[$i]          = $user;
                $userArray[$i]['setID'] = $i;
                $i++;
            }
        }
        return Datatables::of($userArray)
            ->addColumn('action', function ($user) {
                return '<a href="' . route('admin.deliveryboys.show', $user) . '" class="btn btn-sm btn-icon float-left btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="far fa-eye"></i></a><a href="' . route('admin.deliveryboys.edit', $user) . '" class="btn btn-sm btn-icon float-left btn-primary ml-2"data-toggle="tooltip" data-placement="top" title="Edit"><i class="far fa-edit"></i></a>';
            })
            ->addColumn('image', function ($user) {
                return '<figure class="avatar mr-2"><img src="' . $user->images . '" alt=""></figure>';
            })
            ->addColumn('name', function ($user) {
                return $user->name;
            })
            ->editColumn('id', function ($user) {
                return $user->setID;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function history(Request $request)
    {
        if (request()->ajax()) {
            $orders = Order::where(['delivery_boy_id' => $request->delivery_boy_id])->orderBy('id', 'desc')->get();

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
                    return '<a href="' . route('admin.orders.show',
                        $order) . '" class="btn btn-sm btn-icon btn-info"><i class="far fa-eye"></i></a>';
                })
                ->editColumn('user_id', function ($order) {
                    return (!blank($order->user) ? Str::limit($order->user->first_name . ' ' . $order->user->last_name,
                        20) : '');
                })
                ->editColumn('created_at', function ($order) {
                    return Carbon::parse($order->created_at)->format('d M Y, h:i A');
                })
                ->editColumn('status', function ($order) {
                    return trans('order_status.' . $order->status);
                })
                ->editColumn('id', function ($order) {
                    return $order->setID;
                })->make(true);
        }
    }

    private function username($email)
    {
        $emails = explode('@', $email);
        return $emails[0] . mt_rand();
    }

}
