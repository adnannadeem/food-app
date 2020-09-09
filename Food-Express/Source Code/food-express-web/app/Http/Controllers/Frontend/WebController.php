<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;

class WebController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['site_title'] = 'Frontend';
    }

    public function index()
    {
        $this->data['categories'] = Category::pluck('name', 'id');
        $this->data['shopProducts'] = ShopProduct::take(4)->latest()->get();
        return view('welcome', $this->data);
    }

    public function shopProduct($shopName, $productSlug)
    {

        $shop    = Shop::where(['slug' => $shopName])->first();
        $product = Product::where(['slug' => $productSlug])->first();
        if (!blank($shop) && !blank($product)) {
            $this->data['site_title']  = $product->name;
            $this->data['shopProduct'] = ShopProduct::where(['shop_id' => $shop->id, 'product_id' => $product->id])->first();
            return view('frontend.shop_product', $this->data);
        }
        return abort(404);
    }
}
