<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ShopProductCrudResource;
use App\Http\Resources\v1\ShopProductResource;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\ShopProductOption;
use App\Models\ShopProductVariation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopProductController extends Controller
{
    /**
     * @param Shop $shop
     *
     * @return ShopProductResource
     */
    public function action(Shop $shop)
    {
        $shop->load(['products']);
        $shopProduct = ShopProduct::where(['shop_id' => $shop->id])->get()->pluck('unit_price', 'product_id');
        $shop->products->map(function ($query) use ($shopProduct, $shop) {
            $query['unit_price']  = isset($shopProduct[$query->id]) ? $shopProduct[$query->id] : 0;
            $query['stock_count'] = $query->stockCount($shop->id);
            $query['in_stock']    = $query->inStock($shop->id);
            return $query;
        });
        return new ShopProductResource($shop);
    }

    public function store(Request $request, $shop_id)
    {
        $requestArray = [
            'product_type' => 'required|numeric',
            'product_id'   => 'required|numeric',
        ];

        if ($request->product_type == 5) {
            $requestArray['unit_price'] = 'required|numeric';
            $requestArray['quantity']   = 'required|numeric';
        } else if ($request->product_type == 10) {
            $requestArray['variations'] = 'json';
        }

        if ($request->product_type != '') {
            $requestArray['options'] = 'json';
        }
        $validator = Validator::make($request->all(), $requestArray);

        $getShopProduct = ShopProduct::where(['shop_id' => $shop_id, 'product_id' => $request->product_id])->first();
        $validator->after(function ($validator) use ($request, $getShopProduct) {
            $variations = json_decode($request->variations, true);
            if ($request->product_type == 10 && blank($variations)) {
                $validator->errors()->add('variations', 'This field is required');
            }

            if (!blank($getShopProduct)) {
                $validator->errors()->add('product_id', 'This product already assign.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status'  => 401,
                'message' => $validator->errors(),
            ], 401);
        }

        if ($request->product_type == 5) {
            $shopProduct               = new ShopProduct;
            $shopProduct->shop_id      = $shop_id;
            $shopProduct->product_id   = $request->get('product_id');
            $shopProduct->unit_price   = $request->get('unit_price');
            $shopProduct->quantity     = $request->get('quantity');
            $shopProduct->creator_type = User::class;
            $shopProduct->editor_type  = User::class;
            $shopProduct->creator_id   = 1;
            $shopProduct->editor_id    = 1;
            $shopProduct->save();
        } else if ($request->product_type == 10) {

            $shopProduct               = new ShopProduct;
            $shopProduct->shop_id      = $shop_id;
            $shopProduct->product_id   = $request->get('product_id');
            $shopProduct->unit_price   = 0;
            $shopProduct->quantity     = 0;
            $shopProduct->creator_type = User::class;
            $shopProduct->editor_type  = User::class;
            $shopProduct->creator_id   = 1;
            $shopProduct->editor_id    = 1;
            $shopProduct->save();

            if (!blank($request->variations)) {
                $variations = json_decode($request->variations, true);

                $key = array_key_first($variations);

                $smallPrice    = isset($variations[$key]) ? $variations[$key]['price'] : 0;
                $smallQuantity = isset($variations[$key]) ? $variations[$key]['quantity'] : 0;
                $i             = 0;

                $shopProductVariationArray = [];
                foreach ($variations as $variation) {
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
        }

        if (!blank($shopProduct) && !blank($request->options)) {
            $i       = 0;
            $options = json_decode($request->options);

            $shopProductOptionArray = [];
            foreach ($options as $option) {
                if ($option->name == '' || $option->price == '') {
                    continue;
                }
                $shopProductOptionArray[$i]['shop_product_id'] = $shopProduct->id;
                $shopProductOptionArray[$i]['product_id']      = $request->product_id;
                $shopProductOptionArray[$i]['shop_id']         = $shop_id;
                $shopProductOptionArray[$i]['name']            = $option->name;
                $shopProductOptionArray[$i]['price']           = $option->price;
                $i++;
            }
            ShopProductOption::insert($shopProductOptionArray);
        }

        return response()->json([
            'status'  => 200,
            'message' => 'The Shop product Successfully Created',
        ], 200);
    }

    public function update(Request $request, $shop_id, $id)
    {
        $shopProduct = ShopProduct::find($id);

        if (blank($shopProduct)) {
            return response()->json([
                'status'  => 404,
                'message' => 'The shop products not found',
            ]);
        }

        $requestArray = [
            'product_type' => 'required|numeric',
            'product_id'   => 'required|numeric',
        ];

        if ($request->product_type == 5) {
            $requestArray['unit_price'] = 'required|numeric';
            $requestArray['quantity']   = 'required|numeric';
        } else if ($request->product_type == 10) {
            $requestArray['variations'] = 'json';
        }

        if ($request->product_type != '') {
            $requestArray['options'] = 'json';
        }
        $validator = Validator::make($request->all(), $requestArray);

        $getShopProduct = ShopProduct::where(['shop_id' => $shop_id, 'product_id' => $request->product_id])->where('id', '!=', $id)->first();
        $validator->after(function ($validator) use ($request, $getShopProduct) {
            $variations = json_decode($request->variations, true);
            if ($request->product_type == 10 && blank($variations)) {
                $validator->errors()->add('variations', 'This field is required');
            }

            if (!blank($getShopProduct)) {
                $validator->errors()->add('product_id', 'This product already assign.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status'  => 401,
                'message' => $validator->errors(),
            ], 401);
        }

        ShopProductOption::where('shop_product_id', $shopProduct->id)->delete();

        if ($request->product_type == 5) {
            $shopProduct->shop_id      = $shop_id;
            $shopProduct->product_id   = $request->get('product_id');
            $shopProduct->unit_price   = $request->get('unit_price');
            $shopProduct->quantity     = $request->get('quantity');
            $shopProduct->creator_type = User::class;
            $shopProduct->editor_type  = User::class;
            $shopProduct->creator_id   = 1;
            $shopProduct->editor_id    = 1;
            $shopProduct->save();
        } else if ($request->product_type == 10) {
            $shopProduct->shop_id      = $shop_id;
            $shopProduct->product_id   = $request->get('product_id');
            $shopProduct->unit_price   = 0;
            $shopProduct->quantity     = 0;
            $shopProduct->creator_type = User::class;
            $shopProduct->editor_type  = User::class;
            $shopProduct->creator_id   = 1;
            $shopProduct->editor_id    = 1;
            $shopProduct->save();

            if (!blank($request->variations)) {
                $variations = json_decode($request->variations, true);

                $key = array_key_first($variations);

                $smallPrice    = isset($variations[$key]) ? $variations[$key]['price'] : 0;
                $smallQuantity = isset($variations[$key]) ? $variations[$key]['quantity'] : 0;

                $shopProductVariation = ShopProductVariation::where(['shop_id' => $shop_id, 'product_id' => $shopProduct->product_id])->get()->pluck('id', 'id')->toArray();

                $variationArray = [];
                foreach ($variations as $variation) {
                    $key = $variation['id'];

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
        }

        if (!blank($shopProduct) && !blank($request->options)) {
            $i       = 0;
            $options = json_decode($request->options);

            $shopProductOptionArray = [];
            foreach ($options as $option) {
                if ($option->name == '' || $option->price == '') {
                    continue;
                }
                $shopProductOptionArray[$i]['shop_product_id'] = $shopProduct->id;
                $shopProductOptionArray[$i]['product_id']      = $request->product_id;
                $shopProductOptionArray[$i]['shop_id']         = $shop_id;
                $shopProductOptionArray[$i]['name']            = $option->name;
                $shopProductOptionArray[$i]['price']           = $option->price;
                $i++;
            }
            ShopProductOption::insert($shopProductOptionArray);
        }

        return response()->json([
            'status'  => 200,
            'message' => 'The Shop product Successfully Updated',
        ], 200);
    }

    public function product($shop_id)
    {
        $shopProducts = ShopProduct::with('shop')->where(['shop_id' => $shop_id])->get();
        $shopProducts->map(function ($query) use ($shop_id) {
            $query['stock_count'] = $query->product->stockCount($shop_id);
            $query['in_stock']    = $query->product->inStock($shop_id);
            return $query;
        });
        $shopProducts->load(['productvariations', 'productoptions']);

        if (!blank($shopProducts)) {
            return response()->json([
                'status' => 200,
                'data'   => ShopProductCrudResource::collection($shopProducts),
            ]);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'The shop products not found',
        ]);
    }

    public function show($shop_id, $id)
    {
        $shopProduct                = ShopProduct::where(['shop_id' => $shop_id, 'id' => $id])->first();
        $shopProduct['stock_count'] = $shopProduct->product->stockCount($shop_id);
        $shopProduct['in_stock']    = $shopProduct->product->inStock($shop_id);
        $shopProduct->load(['productvariations', 'productoptions']);

        if (!blank($shopProduct)) {
            return response()->json([
                'status' => 200,
                'data'   => new ShopProductCrudResource($shopProduct),
            ]);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'The shop product not found',
        ]);
    }

    public function delete($shop_id, $id)
    {
        $shopProduct = ShopProduct::where(['shop_id' => $shop_id, 'id' => $id])->first();
        if (!blank($shopProduct)) {
            $shopProduct->delete();
            ShopProductVariation::where('shop_product_id', $shopproduct->id)->delete();
            ShopProductOption::where('shop_product_id', $shopproduct->id)->delete();
            return response()->json([
                'status'  => 200,
                'message' => 'The shop product deleted successfully',
            ]);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'The shop product not found',
        ]);
    }

}
