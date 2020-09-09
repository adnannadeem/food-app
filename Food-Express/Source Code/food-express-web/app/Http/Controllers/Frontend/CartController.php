<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Models\Product;
use App\Models\ShopProduct;
use App\Models\ShopProductOption;
use App\Models\ShopProductVariation;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['site_title'] = 'Frontend';
    }

    public function index()
    {
        return view('frontend.cart', $this->data);
    }

    public function store(Request $request)
    {
        $requestArray = [
            'shop_product_id' => 'required|numeric',
            'variations'      => 'nullable|numeric',
            'options.*'       => 'nullable',
        ];
        $validator = Validator::make($request->all(), $requestArray);

        if (!$validator->fails()) {
            $shop_product_id = $request->shop_product_id;
            $shopProduct     = ShopProduct::findOrfail($shop_product_id);

            if ($shopProduct) {
                $cartArray = [];
                $product   = Product::find($shopProduct->product_id);

                $totalPrice     = 0;
                $variationArray = [];
                if ((int) $request->variations) {
                    $variations = ShopProductVariation::find($request->variations);

                    $variationArray['id']    = $variations->id;
                    $variationArray['name']  = $variations->name;
                    $variationArray['price'] = $variations->price;

                    $totalPrice = $variationArray['price'];
                } else {
                    $totalPrice = $shopProduct->unit_price;
                }

                $optionArray = [];
                if (!blank($request->options)) {
                    $options = ShopProductOption::whereIn('id', $request->options)->get();

                    $i = 0;
                    foreach ($options as $option) {
                        $optionArray[$i]['id']    = $option->id;
                        $optionArray[$i]['name']  = $option->name;
                        $optionArray[$i]['price'] = $option->price;
                        $i++;
                        $totalPrice += $option->price;
                    }
                }

                $cartItem = ['id' => $shop_product_id, 'name' => $product->name, 'qty' => 1, 'price' => $totalPrice, 'weight' => 0, 'options' => ['options' => $optionArray, 'variation' => $variationArray, 'images' => $product->images]];

                Cart::add($cartItem);
            }
        }
        return back();
    }

    public function remove($id)
    {
        Cart::remove($id);
        return back()->withSuccess("The Cart Item Remove Successfully");
    }

}
