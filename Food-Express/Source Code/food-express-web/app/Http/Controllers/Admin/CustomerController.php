<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\BackendController;
use App\Http\Requests\CustomerRequest;
use App\User;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class CustomerController extends BackendController
{
    public function __construct()
    {
        $this->data['sitetitle'] = 'Customers';
        $this->data['userRole'] = UserRole::CUSTOMER;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.customer.index', $this->data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->data['user'] = User::where('roles', UserRole::CUSTOMER)->findOrFail($id);
        return view('admin.customer.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['user'] = User::where('roles', UserRole::CUSTOMER)->findOrFail($id);
        return view('admin.customer.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, $id)
    {
        $user             = User::where('roles', UserRole::CUSTOMER)->findOrFail($id);
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
        $user->save();

        if (request()->file('image')) {
            $user->media()->delete();
            $user->addMedia(request()->file('image'))->toMediaCollection('user');
        }

        return redirect(route('admin.customers.index'))->withSuccess('The Data Updated Successfully');
    }

    public function getCustomers()
    {
        $users     = User::where('roles', UserRole::CUSTOMER)->orderBy('id', 'desc')->get();
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
                return '<a href="' . route('admin.customers.show', $user) . '" class="btn btn-sm btn-icon float-left btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="far fa-eye"></i></a><a href="' . route('admin.customers.edit', $user) . '" class="btn btn-sm btn-icon float-left btn-primary ml-2" data-toggle="tooltip" data-placement="top" title="Edit"><i class="far fa-edit"></i></a>';
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

    private function username($email)
    {
        $emails = explode('@', $email);
        return $emails[0] . mt_rand();
    }

}
