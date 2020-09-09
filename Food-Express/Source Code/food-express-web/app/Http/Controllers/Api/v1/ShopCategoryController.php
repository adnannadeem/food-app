<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 22/4/20
 * Time: 11:54 AM
 */

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ShopCategoryResourceCollection;
use App\Http\Resources\v1\ShopResource;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Shop;
use App\Models\ShopProduct;

class ShopCategoryController extends Controller
{
    /**
     * @return ShopCategoryResourceCollection
     */
    public function action($id)
    {
        $shop = Shop::findOrFail($id);

        $product         = ShopProduct::where(['shop_id' => $id])->get()->pluck('product_id')->unique();
        $categoryProduct = CategoryProduct::whereIn('product_id', $product)->get()->pluck('category_id')->unique();
        $category        = Category::whereIn('id', $categoryProduct)->get();

        return response()->json([
            'status' => 200,
            'data'   => ['shop' => new ShopResource($shop), 'categories' => new ShopCategoryResourceCollection($category)]
        ]);
    }
}
