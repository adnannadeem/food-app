<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\BackendController;
use App\Http\Requests\LocationRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

use App\Libraries\MyString;

class LocationController extends BackendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['sitetitle'] = 'Locations';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.location.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.location.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LocationRequest $request)
    {
        $location         = new Location;
        $location->name   = $request->name;
        $location->status = $request->status;
        $location->save();

        return redirect(route('admin.location.index'))->withSuccess('The Data Inserted Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['location'] = Location::findOrFail($id);
        return view('admin.location.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LocationRequest $request, $id)
    {
        $location         = Location::findOrFail($id);
        $location->name   = $request->name;
        $location->status = $request->status;
        $location->save();
        return redirect(route('admin.location.index'))->withSuccess('The Data Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Location::findOrFail($id)->delete();
        return redirect(route('admin.location.index'))->withSuccess('The Data Deleted Successfully');
    }

    public function getLocation(Request $request)
    {
        if (request()->ajax()) {
            if (!empty($request->status) && (int) $request->status) {
                $locations = Location::where(['status' => $request->status])->orderBy('id', 'desc')->get();
            } else {
                $locations = Location::orderBy('id', 'desc')->get();
            }

            $i             = 1;
            $locationArray = [];
            if (!blank($locations)) {
                foreach ($locations as $location) {
                    $locationArray[$i]          = $location;
                    $locationArray[$i]['name']  = Str::limit($location->name, 50);
                    $locationArray[$i]['setID'] = $i;
                    $i++;
                }
            }
            return Datatables::of($locationArray)
                ->addColumn('action', function ($location) {
                    return '<a href="' . route('admin.location.edit', $location) . '" class="btn btn-sm btn-icon float-left btn-primary" 
                    data-toggle="tooltip" data-placement="top" title="Edit"
                    ><i class="far fa-edit"></i></a><form class="float-left pl-2" action="' . route('admin.location.destroy', $location) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button></form>';
                })
                ->editColumn('status', function ($location) {
                    return ($location->status == 5 ? trans('statuses.' . Status::ACTIVE) : trans('statuses.' . Status::INACTIVE));
                })
                ->editColumn('id', function ($location) {
                    return $location->setID;
                })
                ->make(true);
        }
    }
}
