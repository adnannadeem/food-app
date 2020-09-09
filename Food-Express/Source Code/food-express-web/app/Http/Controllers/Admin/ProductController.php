<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductRequested;
use App\Enums\ProductStatus;
use App\Enums\Status;
use App\Http\Controllers\BackendController;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

class ProductController extends BackendController
{

    /**
     * ProductController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['sitetitle'] = 'Products';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.product.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['categories'] = Category::where(['status' => Status::ACTIVE])->get();
        return view('admin.product.create', $this->data);
    }

    /**
     * @param ProductRequest $request
     * @return mixed
     */
    public function store(ProductRequest $request)
    {
        $product              = new Product;
        $product->name        = $request->get('name');
        $product->description = $request->get('description');
        $product->status      = $request->get('status');
        $product->unit_price  = $request->get('unit_price');
        $product->requested   = ProductRequested::NON_REQUESTED;
        $product->save();

        $product->categories()->sync($request->get('categories'));

        //Store Image
        if ( !blank($request->input('document')) ) {
            foreach ($request->input('document', []) as $file) {
                $product->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('products');
            }
        }

        return redirect()->route('admin.products.index')->withSuccess('The data inserted successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Product $product)
    {
        return view('admin.product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $this->data['product']            = $product;
        $this->data['categories']         = Category::where(['status' => Status::ACTIVE])->get();
        $this->data['product_categories'] = $product->categories()->pluck('id')->toArray();

        return view('admin.product.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param ProductRequest $request
     * @param $id
     * @return mixed
     */
    public function update(ProductRequest $request, $id)
    {
        $product              = Product::findOrFail($id);
        $product->name        = $request->get('name');
        $product->description = $request->get('description');
        $product->status      = $request->get('status');
        $product->unit_price  = $request->get('unit_price');
        $product->save();

        $product->categories()->sync($request->get('categories'));

        return redirect()->route('admin.products.index')->withSuccess('The data updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('admin.products.index')->withSuccess('The Data Deleted Successfully');
    }

    public function getProduct(Request $request)
    {
        if (request()->ajax()) {

            $queryArray = [];
            if (!empty($request->status) && (int) $request->status) {
                $queryArray['status'] = $request->status;
            }
            if ($request->requested != '') {
                $queryArray['requested'] = $request->requested;
            }

            if (!blank($queryArray)){
                $products = Product::with('categories')->where($queryArray)->orderBy('id', 'desc')->get();
            }



            else {
                $products = Product::with('categories')->orderBy('id', 'desc')->get();
            }

            $i            = 1;
            $productArray = [];
            if (!blank($products)) {
                foreach ($products as $product) {
                    $productArray[$i]          = $product;
                    $productArray[$i]['setID'] = $i;
                    $i++;
                }
            }
            return Datatables::of($productArray)
                ->addColumn('action', function ($product) {
                    return '<a href="' . route('admin.products.show', $product)
                    . '" class="btn btn-sm btn-icon mr-2  float-left btn-info"
                    data-toggle="tooltip" data-placement="top" title="View"
                    >
                            <i class="far fa-eye"></i></a>
                            <a href="' . route('admin.products.edit', $product)
                    . '" class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit">
                            <i class="far fa-edit"></i></a>
                            <form class="float-left pl-2" action="'
                    . route('admin.products.destroy', $product)
                    . '" method="POST">' . method_field('DELETE')
                    . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete">
                            <i class="fa fa-trash"></i></button></form>';
                })
                ->editColumn('categories', function ($product) {
                    $categories = implode(', ', $product->categories()->pluck('name')->toArray());
                    return Str::limit($categories, 30);
                })
                ->editColumn('name', function ($product) {
                    $col = '<p class="p-0 m-0">' . Str::limit($product->name, 20) . '</p>';
                    $col .= '<small class="text-muted">' . Str::limit($product->description, 20) . '</small>';
                    return $col;
                })
                ->editColumn('status', function ($product) {
                    return ($product->status == 5 ? trans('statuses.' . Status::ACTIVE) : trans('statuses.' . Status::INACTIVE));
                })
                ->editColumn('created_at', function ($product) {
                    return $product->created_at->diffForHumans();
                })
                ->editColumn('id', function ($product) {
                    return $product->setID;
                })
                ->rawColumns(['name', 'action'])
                ->make(true);
        }
    }

    public function storeMedia(Request $request)
    {
        $path = storage_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }


    public function updateMedia(Request $request, Product $product)
    {
        $path = storage_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());
        $file->move($path, $name);
        $product->addMedia($path.'/'.$name)->toMediaCollection('products');

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function deleteMedia(Request $request)
    {
        $path = storage_path('tmp/uploads/' . $request->filename);
        if ( file_exists($path) ) {
            unlink($path);
        }
    }

    public function getMedia( Request $request )
    {
        $product       = Product::where('status', ProductStatus::ACTIVE)->find($request->id);
        $admedias       = $product->getMedia('products');

        $retArr = [];
        if ( count($admedias) ) {
            $i = 0;
            foreach ( $admedias as $admedia ) {
                $i++;
                $retArr[ $i ]['name'] = $admedia->file_name;
                $retArr[ $i ]['size'] = $admedia->size;
                $retArr[ $i ]['url']  = asset($admedia->getUrl());
            }
        }
        echo json_encode($retArr);
    }

    public function removeMedia( Request $request )
    {
        $product       = Product::find($request->id);
        $product->deleteMedia($product, $request->media, $request->id);

        return $this->getMedia($request);
    }
}
