<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\BackendController;
use App\Http\Requests\AdminUserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class AdminUserController extends BackendController
{
    public function __construct()
    {
        $this->data['sitetitle'] = 'Administrator';
        $this->data['userRole']  = UserRole::ADMIN;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.adminuser.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.adminuser.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminUserRequest $request)
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
        $user->save();

        if (request()->file('image')) {
            $user->addMedia(request()->file('image'))->toMediaCollection('user');
        }

        return redirect(route('admin.adminusers.index'))->withSuccess('The Data Inserted Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->data['user'] = User::where('roles', UserRole::ADMIN)->findOrFail($id);
        return view('admin.adminuser.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::where('roles', UserRole::ADMIN)->findOrFail($id);
        if (($user->id != 1) || (auth()->id() == 1)) {
            $this->data['user'] = $user;
            return view('admin.adminuser.edit', $this->data);
        }
        return redirect(route('admin.adminusers.index'))->withError('You don\'t have permission to edit this data');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminUserRequest $request, $id)
    {
        $user = User::where('roles', UserRole::ADMIN)->findOrFail($id);

        if (($user->id != 1) || (auth()->id() == 1)) {

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

            return redirect(route('admin.adminusers.index'))->withSuccess('The Data Updated Successfully');
        }
        return redirect(route('admin.adminusers.index'))->withError('You don\'t have permission to update this data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('roles', UserRole::ADMIN)->findOrFail($id);
        if (($user->id != 1) && (auth()->id() == 1)) {
            $user->delete();
            return redirect(route('admin.adminusers.index'))->withSuccess('The Data Deleted Successfully');
        } else {
            return redirect(route('admin.adminusers.index'))->withError('You don\'t have permission to delete this data');
        }
    }

    public function getAdminUsers()
    {
        $users     = User::where('roles', UserRole::ADMIN)->orderBy('id', 'desc')->get();
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
                if (($user->id == auth()->id()) && (auth()->id() == 1)) {
                    return '<a href="' . route('admin.adminusers.show', $user) . '" class="btn btn-sm btn-icon float-left btn-info" 
                    data-toggle="tooltip" data-placement="top" title="View"
                    ><i class="far fa-eye"></i></a><a href="' . route('admin.adminusers.edit', $user) . '" class="btn btn-sm btn-icon float-left btn-primary ml-2" data-toggle="tooltip" data-placement="top" title="Edit"><i class="far fa-edit"></i></a>';
                } else if(auth()->id() == 1) {
                    return '<a href="' . route('admin.adminusers.show', $user) . '" class="btn btn-sm btn-icon float-left btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="far fa-eye"></i></a><a href="' . route('admin.adminusers.edit', $user) . '" class="btn btn-sm btn-icon float-left btn-primary ml-2" data-toggle="tooltip" data-placement="top" title="Edit"><i class="far fa-edit"></i></a><form class="float-left pl-2" action="' . route('admin.adminusers.destroy', $user) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button></form>';
                } else {
                    if($user->id == 1) {
                        return '<a href="' . route('admin.adminusers.show', $user) . '" class="btn btn-sm btn-icon float-left btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="far fa-eye"></i></a>';
                    } else {
                        return '<a href="' . route('admin.adminusers.show', $user) . '" class="btn btn-sm btn-icon float-left btn-info"
                        data-toggle="tooltip" data-placement="top" title="View"><i class="far fa-eye"></i></a><a href="' . route('admin.adminusers.edit', $user) . '" class="btn btn-sm btn-icon float-left btn-primary ml-2"><i class="far fa-edit"></i></a>';
                    }
                }
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
