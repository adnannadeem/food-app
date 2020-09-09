<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 20/4/20
 * Time: 2:41 PM
 */

namespace App\Http\Controllers\Api\v1;


use App\Http\Controllers\Controller;
use App\Http\Resources\v1\SettingResource;
use App\Models\Setting;
use function MongoDB\BSON\toJSON;

class SettingController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $settingArray = [];
        $settings     = Setting::all();
        if ( !blank($settings) ) {
            foreach ( $settings as $setting ) {
                $settingArray[ $setting->key ] = $setting->value;
            }
        }
        return response()->json([
            'status' => 200,
            'data'   => $settingArray,
        ], 200);
    }
}