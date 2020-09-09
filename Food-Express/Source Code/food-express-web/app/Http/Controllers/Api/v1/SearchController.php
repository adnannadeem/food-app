<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ShopStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\SearchShopProductResource;
use App\Http\Resources\v1\SearchShopResource;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Database\Eloquent\Builder;

class SearchController extends Controller
{

    public function shops($shop)
    {
        $shops = Shop::where(['status' => ShopStatus::ACTIVE])->where('name', 'like', '%' . $shop . '%')->get();
        return response()->json([
            'status' => 200,
            'data'   => SearchShopResource::collection($shops),
        ], 200);
    }

    public function shopProducts($shopID, $product)
    {
        $shopProduct = ShopProduct::where(['shop_id' => $shopID])->get()->pluck('unit_price', 'product_id');
        $shopProducts = Shop::find($shopID)->products()->where(function (Builder $query) use ($product) {
            $query->where('products.name', 'like', '%' . $product . '%');
        })->get();

        $shopProducts->map(function($query) use($shopProduct, $shopID) {
            $query['unit_price'] = isset($shopProduct[$query->id]) ? $shopProduct[$query->id] : 0 ;
            $query['stock_count'] = $query->stockCount($shopID);
            $query['in_stock'] = $query->inStock($shopID);
            return $query;
        });

        return response()->json([
            'status' => 200,
            'data'   => SearchShopProductResource::collection($shopProducts),
        ], 200);
    }

}
