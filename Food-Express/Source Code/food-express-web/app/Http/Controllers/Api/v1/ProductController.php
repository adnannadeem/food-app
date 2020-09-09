<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * ProductController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $product = Product::where(['status' => ProductStatus::ACTIVE])->get();
        if (!blank($product)) {
            return response()->json([
                'status' => 200,
                'data'   => new ProductResource($product),
            ]);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'The data not found',
        ]);
    }

}
