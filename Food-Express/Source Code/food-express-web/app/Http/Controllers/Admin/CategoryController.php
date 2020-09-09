<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Controllers\BackendController;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

class CategoryController extends BackendController
{

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['sitetitle'] = 'Categories';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.category.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['categories'] = Category::where(['status' => Status::ACTIVE])->get();
        return view('admin.category.create', $this->data);
    }

    /**
     * @param CategoryRequest $request
     * @return mixed
     */
    public function store(CategoryRequest $request)
    {
        $category              = new Category;
        $category->name        = $request->name;
        $category->description = $request->description;
        $category->parent_id   = $request->parent_id;
        $category->depth       = 0;
        $category->left        = 0;
        $category->right       = 0;
        $category->parent_id   = 0;
        $category->status      = $request->status;
        $category->save();

        //Store Image
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $category->addMediaFromRequest('image')->toMediaCollection('categories');
        }

        return redirect(route('admin.category.index'))->withSuccess('The Data Inserted Successfully');
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
        $this->data['category'] = Category::findOrFail($id);
        return view('admin.category.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        $category              = Category::findOrFail($id);
        $category->name        = $request->name;
        $category->description = $request->description;
        $category->parent_id   = $request->parent_id;
        $category->depth       = 0;
        $category->left        = 0;
        $category->right       = 0;
        $category->parent_id   = 0;
        $category->status      = $request->status;
        $category->save();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $category->media()->delete($id);
            $category->addMediaFromRequest('image')->toMediaCollection('categories');
        }

        return redirect(route('admin.category.index'))->withSuccess('The Data Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return redirect(route('admin.category.index'))->withSuccess('The Data Deleted Successfully');
    }

    public function getCategory(Request $request)
    {
        if (request()->ajax()) {
            if (!empty($request->status) && (int) $request->status) {
                $categorys = Category::where(['status' => $request->status])->orderBy('id', 'desc')->get();
            } else {
                $categorys = Category::orderBy('id', 'desc')->get();
            }

            $i             = 1;
            $categoryArray = [];
            if (!blank($categorys)) {
                foreach ($categorys as $category) {
                    $categoryArray[$i]          = $category;
                    $categoryArray[$i]['setID'] = $i;
                    $i++;
                }
            }
            return Datatables::of($categoryArray)
                ->addColumn('image', function ($category) {
                    if ($category->getFirstMediaUrl('categories')) {
                        return '<img alt="image" src="' . asset($category->getFirstMediaUrl('categories')) . '" class="rounded-circle mr-1 avatar-item">';
                    } else {
                        return '<img alt="image" src="' . asset('assets/img/default/category.png') . '" class="rounded-circle mr-1 avatar-item">';
                    }
                })
                ->addColumn('action', function ($category) {
                    return '<a href="' . route('admin.category.edit', $category) . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit" ><i class="far fa-edit"></i></a><form class="float-left pl-2" action="' . route('admin.category.destroy', $category) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button></form>';
                })
                ->editColumn('description', function ($category) {
                    return Str::limit(strip_tags($category->description), 50);
                })
                ->editColumn('status', function ($category) {
                    return ($category->status == 5 ? trans('statuses.' . Status::ACTIVE) : trans('statuses.' . Status::INACTIVE));
                })
                ->editColumn('id', function ($category) {
                    return $category->setID;
                })
                ->rawColumns(['image', 'action', 'description'])
                ->make(true);
        }
    }
}
