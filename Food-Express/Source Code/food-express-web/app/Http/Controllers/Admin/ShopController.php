<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CurrentStatus;
use App\Enums\OrderStatus;
use App\Enums\Status;
use App\Enums\UserRole;
use App\Http\Controllers\BackendController;
use App\Http\Requests\ShopRequest;
use App\Models\Area;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\ShopProductOption;
use App\Models\ShopProductVariation;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

class ShopController extends BackendController
{
    /**
     * ShopController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data['sitetitle'] = 'Shops';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.shop.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['locations'] = Location::where(['status' => Status::ACTIVE])->get();
        $this->data['areas']     = [];
        $location_id             = old('location_id');
        if ($location_id) {
            $this->data['areas'] = Area::where(['location_id' => $location_id, 'status' => Status::ACTIVE])->get();

        }
        return view('admin.shop.create', $this->data);
    }

    /**
     * @param ShopRequest $request
     * @return mixed
     */
    public function store(ShopRequest $request)
    {
        $user             = new User;
        $user->first_name = $request->get('first_name');
        $user->last_name  = $request->get('last_name');
        $user->email      = $request->get('email');
        $user->username   = $request->username ?? $this->username($request->email);
        $user->phone      = $request->get('phone');
        $user->address    = $request->get('address');
        $user->password   = bcrypt($request->get('password'));
        $user->roles      = UserRole::SHOPOWNER;
        $user->save();

        $shop                  = new Shop;
        $shop->user_id         = $user->id;
        $shop->location_id     = $request->location_id;
        $shop->area_id         = $request->area_id;
        $shop->name            = $request->name;
        $shop->description     = $request->description;
        $shop->delivery_charge = $request->delivery_charge;
        $shop->lat             = $request->lat;
        $shop->long            = $request->long;
        $shop->opening_time    = date('H:i:s', strtotime($request->opening_time));
        $shop->closing_time    = date('H:i:s', strtotime($request->closing_time));
        $shop->address         = $request->shopaddress;
        $shop->current_status  = $request->current_status;
        $shop->status          = $request->status;
        $shop->applied         = false;
        $shop->save();

        //Store Image
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $shop->addMediaFromRequest('image')->toMediaCollection('shops');
        }

        return redirect(route('admin.shop.index'))->withSuccess('The Data Inserted Successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->data['shop']      = Shop::findOrFail($id);
        $this->data['users']     = User::get();
        $this->data['locations'] = Location::where(['status' => Status::ACTIVE])->get();
        $this->data['areas']     = [];
        $location_id             = old('location_id', $this->data['shop']->location_id);
        if ($location_id) {
            $this->data['areas'] = Area::where(['location_id' => $location_id, 'status' => Status::ACTIVE])->get();

        }
        return view('admin.shop.edit', $this->data);
    }

    /**
     * @param ShopRequest $request
     * @param Shop $shop
     * @return mixed
     */
    public function update(ShopRequest $request, Shop $shop)
    {
        $user             = $shop->user;
        $user->first_name = $request->get('first_name');
        $user->last_name  = $request->get('last_name');
        $user->email      = $request->get('email');
        $user->username   = $request->username ?? $this->username($request->email);
        $user->phone      = $request->get('phone');
        $user->address    = $request->get('address');
        $user->roles      = UserRole::SHOPOWNER;

        if(!blank($request->get('password')) && (strlen($request->get('password')) >= 4)) {
            $user->password   = bcrypt($request->get('password'));
        }

        $user->save();

        $shop->location_id     = $request->location_id;
        $shop->area_id         = $request->area_id;
        $shop->name            = $request->name;
        $shop->description     = $request->description;
        $shop->delivery_charge = $request->delivery_charge;
        $shop->lat             = $request->lat;
        $shop->long            = $request->long;
        $shop->opening_time    = date('H:i:s', strtotime($request->opening_time));
        $shop->closing_time    = date('H:i:s', strtotime($request->closing_time));
        $shop->current_status  = $request->current_status;
        $shop->address         = $request->shopaddress;
        $shop->status          = $request->status;
        $shop->applied         = false;
        $shop->save();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $shop->media()->delete($shop->id);
            $shop->addMediaFromRequest('image')->toMediaCollection('shops');
        }

        return redirect(route('admin.shop.index'))->withSuccess('The Data Updated Successfully');
    }

    public function show($id)
    {
        $shop   = Shop::findOrFail($id);
        $orders = Order::where(['shop_id' => $id])->whereDate('created_at', Carbon::today())->get();

        $this->data['total_order']     = $orders->count();
        $this->data['pending_order']   = $orders->where('status', OrderStatus::PENDING)->count();
        $this->data['process_order']   = $orders->where('status', OrderStatus::PROCESS)->count();
        $this->data['completed_order'] = $orders->where('status', OrderStatus::COMPLETED)->count();

        if (blank($shop->user)) {
            return redirect(route('admin.shop.index'))->withError('The user not found.');
        }
        $this->data['shop'] = $shop;
        $this->data['user'] = $shop->user;
        return view('admin.shop.show', $this->data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Shop::findOrFail($id)->delete();
        return redirect(route('admin.shop.index'))->withSuccess('The Data Deleted Successfully');
    }

    public function getshop(Request $request)
    {
        if (request()->ajax()) {
            $queryArray = [];
            if (!empty($request->status) && (int) $request->status) {
                $queryArray['status'] = $request->status;
            }
            if ($request->applied != '') {
                $queryArray['applied'] = $request->applied;
            }

            if (!blank($queryArray)) {
                $shops = Shop::where($queryArray)->orderBy('id', 'desc')->get();
            } else {
                $shops = Shop::orderBy('id', 'desc')->get();
            }

            $i         = 1;
            $shopArray = [];
            if (!blank($shops)) {
                foreach ($shops as $shop) {
                    $shopArray[$i]          = $shop;
                    $shopArray[$i]['name']  = Str::limit($shop->name, 20);
                    $shopArray[$i]['setID'] = $i;
                    $i++;
                }
            }
            return Datatables::of($shopArray)
                ->addColumn('action', function ($shop) {
                    return '<a href="' . route('admin.shop.show', $shop) . '"
                            class="btn btn-sm btn-icon float-left btn-info mr-2" data-toggle="tooltip" data-placement="top" title="View"> <i class="far fa-eye"></i></a><a href="' . route('admin.shop.products', $shop) . '"
                            class="btn btn-sm btn-icon float-left btn-success mr-2" data-toggle="tooltip" data-placement="top" title="Add Product"> <i class="far fa-list-alt"></i></a>
                            <a href="' . route('admin.shop.edit', $shop) . '"
                            class="btn btn-sm btn-icon float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="far fa-edit"></i></a>
                            <form class="float-left pl-2" action="' . route('admin.shop.destroy', $shop) . '" method="POST">'
                    . method_field('DELETE') . csrf_field() .
                        '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></button>
                            </form>';
                })
                ->editColumn('user_id', function ($shop) {
                    return Str::limit($shop->user->name ?? null, 20);
                })
                ->editColumn('location_id', function ($shop) {
                    return Str::limit($shop->location->name ?? null, 20);
                })
                ->editColumn('status', function ($shop) {
                    return ($shop->status == 5 ? trans('statuses.' . Status::ACTIVE) : trans('statuses.' . Status::INACTIVE));
                })
                ->editColumn('current_status', function ($shop) {
                    return ($shop->current_status == 5 ? trans('current_statuses.' . CurrentStatus::YES) : trans('current_statuses.' . CurrentStatus::NO));
                })
                ->editColumn('id', function ($shop) {
                    return $shop->setID;
                })
                ->make(true);
        }
    }

    public function getArea(Request $request)
    {
        echo "<option value=''>" . __('Select Area') . "</option>";
        $location_id = $request->location_id;
        if (is_numeric($location_id)) {
            $areas = Area::where(['location_id' => $location_id, 'status' => Status::ACTIVE])->get();
            if (!blank($areas)) {
                foreach ($areas as $area) {
                    echo "<option value='" . $area->id . "'>" . $area->name . "</option>";
                }
            }
        }
    }

    private function username($email)
    {
        $emails = explode('@', $email);
        return $emails[0] . mt_rand();
    }

    /**
     * @param Shop $shop
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function products(Shop $shop)
    {
        $this->data['shop']         = $shop;
        $this->data['shopProducts'] = $shop->shopproducts->sortByDesc('id');
        return view('admin.shop.products', $this->data);
    }

    public function productAdd($shop_id)
    {
        $this->data['shop']     = Shop::find($shop_id);
        $this->data['products'] = Product::where(['status' => Status::ACTIVE])->get();
        return view('admin.shop.productAdd', $this->data);
    }

    public function productStore(Request $request, $shop_id)
    {
        $requestArray = [
            'product_type' => 'required',
            'product_id'   => 'required',
        ];

        if ($request->product_type == 5) {
            $requestArray['unit_price'] = 'required|numeric|min:0';
            $requestArray['quantity']   = 'required|numeric|min:1';
        } else if ($request->product_type == 10) {
            $requestArray['variation.*.name']     = 'required';
            $requestArray['variation.*.price']    = 'required|numeric|min:0';
            $requestArray['variation.*.quantity'] = 'required|numeric|min:1';
        }

        if ($request->product_type != '') {
            $requestArray['option.*.name']  = 'nullable';
            $requestArray['option.*.price'] = 'nullable|min:0';
        }
        $validator = Validator::make($request->all(), $requestArray);

        $getShopProduct = ShopProduct::where(['shop_id' => $shop_id, 'product_id' => $request->product_id])->first();
        $validator->after(function ($validator) use ($getShopProduct) {
            if (!blank($getShopProduct)) {
                $validator->errors()->add('product_id', 'This product already assign.');
            }
        });

        if ($validator->fails()) {
            $request->session()->flash('variation', array_keys($request->variation));
            $request->session()->flash('option', array_keys($request->option));
            return redirect(route('admin.shop.products.create', $shop_id))->withErrors($validator)->withInput();
        }

        if ($request->product_type == 5) {
            $shopProduct             = new ShopProduct;
            $shopProduct->shop_id    = $shop_id;
            $shopProduct->product_id = $request->get('product_id');
            $shopProduct->unit_price = $request->get('unit_price');
            $shopProduct->quantity   = $request->get('quantity');
            $shopProduct->save();
        } else if ($request->product_type == 10) {

            $shopProduct             = new ShopProduct;
            $shopProduct->shop_id    = $shop_id;
            $shopProduct->product_id = $request->get('product_id');
            $shopProduct->unit_price = 0;
            $shopProduct->quantity   = 0;
            $shopProduct->save();

            $key = array_key_first($request->variation);

            $smallPrice    = isset($request->variation[$key]) ? $request->variation[$key]['price'] : 0;
            $smallQuantity = isset($request->variation[$key]) ? $request->variation[$key]['quantity'] : 0;
            $i             = 0;

            $shopProductVariationArray = [];
            foreach ($request->variation as $variation) {
                if ($variation['price'] < $smallPrice) {
                    $smallPrice = $variation['price'];
                }
                if ($variation['quantity'] < $smallQuantity) {
                    $smallQuantity = $variation['quantity'];
                }

                $shopProductVariationArray[$i]['shop_product_id'] = $shopProduct->id;
                $shopProductVariationArray[$i]['product_id']      = $request->product_id;
                $shopProductVariationArray[$i]['shop_id']         = $shop_id;
                $shopProductVariationArray[$i]['name']            = $variation['name'];
                $shopProductVariationArray[$i]['price']           = $variation['price'];
                $shopProductVariationArray[$i]['quantity']        = $variation['quantity'];
                $i++;
            }
            ShopProductVariation::insert($shopProductVariationArray);

            $shopProduct->unit_price = $smallPrice;
            $shopProduct->quantity   = $smallQuantity;
            $shopProduct->save();

        }

        if (!blank($shopProduct) && !blank($request->option)) {
            $i                      = 0;
            $shopProductOptionArray = [];
            foreach ($request->option as $option) {
                if ($option['name'] == '' || $option['price'] == '') {
                    continue;
                }

                $shopProductOptionArray[$i]['shop_product_id'] = $shopProduct->id;
                $shopProductOptionArray[$i]['product_id']      = $request->product_id;
                $shopProductOptionArray[$i]['shop_id']         = $shop_id;
                $shopProductOptionArray[$i]['name']            = $option['name'];
                $shopProductOptionArray[$i]['price']           = $option['price'];
                $i++;
            }

            ShopProductOption::insert($shopProductOptionArray);
        }

        return redirect(route('admin.shop.products', $shop_id))->withSuccess("The Data Inserted Successfully");
    }

    /**
     * @param Request $request
     * @param Shop $shop
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function shopProductDelete(Request $request, Shop $shop, ShopProduct $shopproduct)
    {
        if (!blank($shopproduct)) {
            ShopProduct::findOrFail($shopproduct->id)->delete();
            ShopProductVariation::where('shop_product_id', $shopproduct->id)->delete();
            ShopProductOption::where('shop_product_id', $shopproduct->id)->delete();
        }
        return redirect()->route('admin.shop.products', $shop)->withSuccess('The Shop Product Deleted Successfully');
    }

    public function shopProductEdit(Shop $shop, ShopProduct $shopproduct)
    {
        $this->data['shop']               = $shop;
        $this->data['products']           = Product::where(['status' => Status::ACTIVE])->get();
        $this->data['shopproduct']        = $shopproduct;
        $this->data['product_type']       = !blank($shopproduct->productvariations) ? 10 : 5;
        $this->data['product_variations'] = $shopproduct->productvariations;
        $this->data['product_options']    = $shopproduct->productoptions;

        return view('admin.shop.productEdit', $this->data);
    }

    public function shopProductUpdate(Request $request, Shop $shop, $shop_product_id)
    {
        $shopProduct = ShopProduct::findOrFail($shop_product_id);
        $shop_id     = $shop->id;

        $requestArray = [
            'product_type' => 'required',
            'product_id'   => 'required',
        ];

        if ($request->product_type == 5) {
            $requestArray['unit_price'] = 'required|numeric|min:0';
            $requestArray['quantity']   = 'required|numeric|min:1';
        } else if ($request->product_type == 10) {
            $requestArray['variation.*.name']     = 'required';
            $requestArray['variation.*.price']    = 'required|numeric|min:0';
            $requestArray['variation.*.quantity'] = 'required|numeric|min:1';
        }

        if ($request->product_type != '') {
            $requestArray['option.*.name']  = 'nullable';
            $requestArray['option.*.price'] = 'nullable|numeric|min:0';
        }
        $validator = Validator::make($request->all(), $requestArray);

        $getShopProduct = ShopProduct::where(['shop_id' => $shop_id, 'product_id' => $request->product_id])->where('id', '!=', $shop_product_id)->first();
        $validator->after(function ($validator) use ($getShopProduct) {
            if (!blank($getShopProduct)) {
                $validator->errors()->add('product_id', 'This product already assign.');
            }
        });

        if ($validator->fails()) {
            $request->session()->flash('variation', array_keys($request->variation));
            $request->session()->flash('option', array_keys($request->option));
            return redirect(route('admin.shop.shopproduct.edit', [$shop, $shopProduct]))->withErrors($validator)->withInput();
        }

        ShopProductOption::where('shop_product_id', $shopProduct->id)->delete();

        if ($request->product_type == 5) {
            $shopProduct->shop_id    = $shop_id;
            $shopProduct->product_id = $request->get('product_id');
            $shopProduct->unit_price = $request->get('unit_price');
            $shopProduct->quantity   = $request->get('quantity');
            $shopProduct->save();
        } else if ($request->product_type == 10) {
            $shopProduct->shop_id    = $shop_id;
            $shopProduct->product_id = $request->get('product_id');
            $shopProduct->unit_price = 0;
            $shopProduct->quantity   = 0;
            $shopProduct->save();

            $key = array_key_first($request->variation);

            $smallPrice    = isset($request->variation[$key]) ? $request->variation[$key]['price'] : 0;
            $smallQuantity = isset($request->variation[$key]) ? $request->variation[$key]['quantity'] : 0;

            $shopProductVariation = ShopProductVariation::where(['shop_id' => $shop_id, 'product_id' => $shopProduct->product_id])->get()->pluck('id', 'id')->toArray();

            $variationArray = [];
            foreach ($request->variation as $key => $variation) {
                $variationArray[$key] = $key;

                if ($variation['price'] < $smallPrice) {
                    $smallPrice = $variation['price'];
                }
                if ($variation['quantity'] < $smallQuantity) {
                    $smallQuantity = $variation['quantity'];
                }

                if (isset($shopProductVariation[$key])) {
                    $shopProductVariationItem = ShopProductVariation::where(['shop_id' => $shop_id, 'product_id' => $shopProduct->product_id, 'id' => $key])->first();

                    $shopProductVariationItem->shop_product_id = $shopProduct->id;
                    $shopProductVariationItem->product_id      = $request->product_id;
                    $shopProductVariationItem->shop_id         = $shop_id;
                    $shopProductVariationItem->name            = $variation['name'];
                    $shopProductVariationItem->price           = $variation['price'];
                    $shopProductVariationItem->quantity        = $variation['quantity'];
                    $shopProductVariationItem->save();
                } else {
                    $shopProductVariationArray['shop_product_id'] = $shopProduct->id;
                    $shopProductVariationArray['product_id']      = $request->product_id;
                    $shopProductVariationArray['shop_id']         = $shop_id;
                    $shopProductVariationArray['name']            = $variation['name'];
                    $shopProductVariationArray['price']           = $variation['price'];
                    $shopProductVariationArray['quantity']        = $variation['quantity'];

                    ShopProductVariation::insert($shopProductVariationArray);
                }
            }

            $shopProduct->unit_price = $smallPrice;
            $shopProduct->quantity   = $smallQuantity;
            $shopProduct->save();

            $deleteArray = array_diff($shopProductVariation, $variationArray);

            if (!blank($deleteArray)) {
                ShopProductVariation::whereIn('id', $deleteArray)->delete();
            }
        }

        if (!blank($shopProduct) && !blank($request->option)) {
            $i                      = 0;
            $shopProductOptionArray = [];
            foreach ($request->option as $option) {

                if ($option['name'] == '' || $option['price'] == '') {
                    continue;
                }

                $shopProductOptionArray[$i]['shop_product_id'] = $shopProduct->id;
                $shopProductOptionArray[$i]['product_id']      = $request->product_id;
                $shopProductOptionArray[$i]['shop_id']         = $shop_id;
                $shopProductOptionArray[$i]['name']            = $option['name'];
                $shopProductOptionArray[$i]['price']           = $option['price'];
                $i++;
            }

            ShopProductOption::insert($shopProductOptionArray);
        }

        return redirect(route('admin.shop.products', $shop_id))->withSuccess("The Data Updated Successfully");
    }

}
