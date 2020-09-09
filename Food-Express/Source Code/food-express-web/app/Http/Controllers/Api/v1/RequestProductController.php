<?php
/**
 * Created by PhpStorm.
 * User: Dipok Hlader
 * Date: 7/14/20
 * Time: 6:18 PM
 */

namespace App\Http\Controllers\Api\v1;

use App\Enums\ProductRequested;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RequestProductRequest;
use App\Http\Resources\v1\RequestProductResource;
use App\Models\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class RequestProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $product = Product::where(['creator_id' => auth()->user()->id, 'creator_type' => User::class])->get();

        return response()->json([
            'status' => 200,
            'data'   => RequestProductResource::collection($product),
        ]);
    }

    public function store(Request $request)
    {

        $validator = new RequestProductRequest();
        $validator = Validator::make($request->all(), $validator->rules());

        if ($request->get('image') != '') {
            $validType      = ['jpg', 'png', 'jpeg'];
            $fileName       = $request->get('fileName');
            $validExtension = false;
            if (empty($fileName)) {
                $validExtension = true;
            } else {
                $extension = explode('.', $fileName);
                $extension = strtolower(end($extension));

                if (!in_array($extension, $validType)) {
                    $validExtension = true;
                }
            }

            $validator->after(function ($validator) use ($validExtension) {
                if ($validExtension) {
                    $validator->errors()->add('image', 'This image type was invalid.');
                }
            });
        }

        if (!$validator->fails()) {
            $product              = new Product;
            $product->name        = $request->get('name');
            $product->description = $request->get('description');
            $product->unit_price  = $request->get('mrp');
            $product->status      = Status::INACTIVE;
            $product->requested   = ProductRequested::REQUESTED;
            $product->save();

            if ($request->get('image') != '') {

                $realImage = base64_decode($request->get('image'));
                file_put_contents($request->get('fileName'), $realImage);

                $url = public_path($request->get('fileName'));

                $product->media()->delete();
                $product->addMediaFromUrl($url)->toMediaCollection('products');

                File::delete($url);
            }

            $product->categories()->sync(json_decode($request->get('categories')));

            return response()->json([
                'status'  => 200,
                'message' => 'Successfully Created Product',
                'data'    => new RequestProductResource($product),
            ], 200);
        } else {
            return response()->json([
                'status'  => 400,
                'message' => $validator->errors(),
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::where(['id' => $id, 'creator_id' => auth()->user()->id, 'creator_type' => User::class])->first();
        if (!blank($product)) {
            $validator = new RequestProductRequest();
            $validator = Validator::make($request->all(), $validator->rules());

            if ($request->get('image') != '') {
                $validType      = ['jpg', 'png', 'jpeg'];
                $fileName       = $request->get('fileName');
                $validExtension = false;
                if (empty($fileName)) {
                    $validExtension = true;
                } else {
                    $extension = explode('.', $fileName);
                    $extension = strtolower(end($extension));

                    if (!in_array($extension, $validType)) {
                        $validExtension = true;
                    }
                }

                $validator->after(function ($validator) use ($validExtension) {
                    if ($validExtension) {
                        $validator->errors()->add('image', 'This image type was invalid.');
                    }
                });
            }

            if (!$validator->fails()) {
                $product->name        = $request->get('name');
                $product->description = $request->get('description');
                $product->unit_price  = $request->get('mrp');
                $product->save();

                if ($request->get('image') != '') {

                    $realImage = base64_decode($request->get('image'));
                    file_put_contents($request->get('fileName'), $realImage);

                    $url = public_path($request->get('fileName'));

                    $product->media()->delete();
                    $product->addMediaFromUrl($url)->toMediaCollection('products');

                    File::delete($url);
                }

                $product->categories()->sync(json_decode($request->get('categories')));

                return response()->json([
                    'status'  => 200,
                    'message' => 'Successfully Updated Product',
                    'data'    => new RequestProductResource($product),
                ], 200);
            } else {
                return response()->json([
                    'status'  => 400,
                    'message' => $validator->errors(),
                ], 400);
            }
        } else {
            return response()->json([
                'status'  => 400,
                'message' => 'Bad Request',
            ], 400);
        }
    }

    public function show($id)
    {
        $product = Product::where(['id' => $id, 'creator_id' => auth()->user()->id, 'creator_type' => User::class])->first();
        if (!blank($product)) {
            return response()->json([
                'status' => 200,
                'data'   => new RequestProductResource($product),
            ], 200);
        } else {
            return response()->json([
                'status'  => 400,
                'message' => 'Bad Request',
            ], 400);
        }
    }

    public function delete($id)
    {
        $product = Product::where(['creator_id' => auth()->user()->id, 'creator_type' => User::class, 'id' => $id])->first();
        if (!blank($product)) {
            $product->delete();
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
