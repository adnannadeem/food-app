<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\ShopStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ShopRequest;
use App\Http\Resources\v1\ShopResource;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    /**
     * ShopController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function show($id)
    {
        $shop = Shop::where(['id' => $id, 'user_id' => auth()->user()->id])->first();
        if (!blank($shop)) {
            return response()->json([
                'status' => 200,
                'data'   => new ShopResource($shop),
            ]);
        }
        return response()->json([
            'status'  => 404,
            'message' => 'The data not found',
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    public function store(Request $request)
    {
        $shop = Shop::where(['user_id' => auth()->user()->id])->first();
        if (blank($shop)) {
            $validator = new ShopRequest();
            $validator = Validator::make($request->all(), $validator->rules());

            if (!$validator->fails()) {
                $shop                  = new Shop;
                $shop->user_id         = auth()->user()->id;
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
                $shop->status          = ShopStatus::INACTIVE;
                $shop->applied         = true;
                $shop->save();

                if ($request->get('image') != '') {
                    $realImage = base64_decode($request->get('image'));
                    file_put_contents($request->get('fileName'), $realImage);

                    $url = public_path($request->get('fileName'));

                    $shop->media()->delete();
                    $shop->addMediaFromUrl($url)->toMediaCollection('shops');

                    File::delete($url);
                }

                return response()->json([
                    'status'  => 200,
                    'message' => 'Successfully Created Shop',
                    'data'    => new ShopResource($shop),
                ], 200);
            } else {
                return response()->json([
                    'status'  => 422,
                    'message' => $validator->errors(),
                ], 422);
            }
        }
        return response()->json([
            'status'  => 422,
            'message' => 'You already created shop',
        ], 422);
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $shop = Shop::where(['id' => $id, 'user_id' => auth()->user()->id])->first();
        if (!blank($shop)) {
            $validator = new ShopRequest($id);
            $validator = Validator::make($request->all(), $validator->rules());
            if (!$validator->fails()) {
                $shop->user_id         = auth()->user()->id;
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
                $shop->applied         = true;
                $shop->save();

                if ($request->get('image') != '') {
                    $realImage = base64_decode($request->get('image'));
                    file_put_contents($request->get('fileName'), $realImage);

                    $url = public_path($request->get('fileName'));

                    $shop->media()->delete();
                    $shop->addMediaFromUrl($url)->toMediaCollection('shops');

                    File::delete($url);
                }

                return response()->json([
                    'status'  => 200,
                    'message' => 'Successfully Updated Shop',
                    'data'    => new ShopResource($shop),
                ], 200);
            } else {
                return response()->json([
                    'status'  => 422,
                    'message' => $validator->errors(),
                ], 422);
            }
        } else {
            return response()->json([
                'status'  => 400,
                'message' => 'Bad Request',
            ], 400);
        }
    }

}
