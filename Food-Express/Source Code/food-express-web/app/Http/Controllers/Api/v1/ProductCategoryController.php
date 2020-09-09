<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 7/15/20
 * Time: 3:35 PM
 */

namespace App\Http\Controllers\Api\v1;


use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ProductCategoryResource;
use App\Models\Category;

class ProductCategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $categories = Category::where(['status' => Status::ACTIVE])->get();

        if (!blank($categories)) {
            return response()->json([
                'status' => 200,
                'data'   => ProductCategoryResource::collection($categories),
            ]);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'The data not found',
        ]);
    }

}