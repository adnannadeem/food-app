<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\BackendController;
use App\Http\Requests\AreaRequest;
use App\Models\Area;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

class AreaController extends BackendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['sitetitle'] = 'Areas';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.area.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['locations'] = Location::where(['status' => Status::ACTIVE])->get();
        return view('admin.area.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AreaRequest $request)
    {
        $area              = new Area;
        $area->name        = $request->name;
        $area->location_id = $request->location_id;
        $area->status      = $request->status;
        $area->save();

        return redirect(route('admin.area.index'))->withSuccess('The Data Inserted Successfully');
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
        $this->data['area']      = Area::findOrFail($id);
        $this->data['locations'] = Location::where(['status' => Status::ACTIVE])->get();
        return view('admin.area.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AreaRequest $request, $id)
    {
        $area              = Area::findOrFail($id);
        $area->name        = $request->name;
        $area->location_id = $request->location_id;
        $area->status      = $request->status;
        $area->save();
        return redirect(route('admin.area.index'))->withSuccess('The Data Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Area::findOrFail($id)->delete();
        return redirect(route('admin.area.index'))->withSuccess('The Data Deleted Successfully');
    }

    public function getArea(Request $request)
    {
        if (request()->ajax()) {
            if (!empty($request->status) && (int) $request->status) {
                $areas = Area::where(['status' => $request->status])->orderBy('id', 'desc')->get();
            } else {
                $areas = Area::orderBy('id', 'desc')->get();
            }

            $i         = 1;
            $areaArray = [];
            if (!blank($areas)) {
                foreach ($areas as $area) {
                    $areaArray[$i]          = $area;
                    $areaArray[$i]['name']  = Str::limit($area->name, 30);
                    $areaArray[$i]['setID'] = $i;
                    $i++;
                }
            }
            return Datatables::of($areaArray)
                ->addColumn('action', function ($area) {
                    return '<a href="' . route('admin.area.edit', $area) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"><i class="far fa-edit"></i></a><form class="float-left pl-2" action="' . route('admin.area.destroy', $area) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button></form>';
                })
                ->editColumn('location_id', function ($area) {
                    return Str::limit($area->location->name ?? null, 30);
                })
                ->editColumn('status', function ($area) {
                    return ($area->status == 5 ? trans('statuses.' . Status::ACTIVE) : trans('statuses.' . Status::INACTIVE));
                })
                ->editColumn('id', function ($area) {
                    return $area->setID;
                })
                ->make(true);
        }
    }
}
