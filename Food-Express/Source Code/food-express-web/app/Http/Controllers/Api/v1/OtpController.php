<?php
/**
 * Created by PhpStorm.
 * User: Dipok Hlader
 * Date: 7/14/20
 * Time: 6:18 PM
 */

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\PrivateUserResource;
use App\Http\Resources\v1\ShopResource;
use App\Models\Otp;
use App\Notifications\OneTimePasswordSend;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{

    public function get_otp(Request $request)
    {
        $email = false;
        $phone = false;

        $requestArray = [
            'otp' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $requestArray);

        if (!blank($request->otp)) {
            if (filter_var($request->otp, FILTER_VALIDATE_EMAIL)) {
                $email = true;
            } else {
                $phone = true;
            }
        }

        $queryArray = [];
        if ($email) {
            $queryArray['email'] = $request->otp;
        } else if ($phone) {
            $queryArray['phone'] = $request->otp;
        }

        $user = [];
        if (!blank($queryArray)) {
            $user = User::where($queryArray)->first();
        }

        $validator->after(function ($validator) use ($user, $request, $email, $phone) {
            if (setting('otp_type_checking') == 'email' && !$email) {
                $validator->errors()->add('otp', 'Please provide valid email address.');
            } else if (setting('otp_type_checking') == 'phone' && !$phone) {
                $validator->errors()->add('otp', 'Please provide valid phone number.');
            } else if (blank($user) && $request->otp != '') {
                $validator->errors()->add('otp', 'This user data not found');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status'  => 401,
                'message' => $validator->errors(),
            ], 401);
        }

        if (!blank($user)) {
            $otp_digit_limit = setting('otp_digit_limit');
            $code            = rand(pow(10, $otp_digit_limit - 1), pow(10, $otp_digit_limit) - 1);
            $otp_expire_time = setting('otp_expire_time') ?? 0;
            $expire_date     = date("Y-m-d H:i:s", strtotime($otp_expire_time . ' minutes'));

            $date = new \DateTime;
            $date->modify("-$otp_expire_time minutes");
            $otp_expire_date = $date->format('Y-m-d H:i:s');
            $otp             = Otp::where(['user_id' => $user->id])->where('expire_date', '>=', $otp_expire_date)->latest('id')->first();
            if (!blank($otp)) {
                $otp->code        = $code;
                $otp->expire_date = $expire_date;
                $otp->save();

                $user->notify(new OneTimePasswordSend($otp->code));

                return response()->json([
                    'status' => 200,
                    'data'   => 'The Code re-generate successfully',
                ], 200);

            } else {
                $otpArray['user_id']     = $user->id;
                $otpArray['email']       = $user->email;
                $otpArray['phone']       = $user->phone;
                $otpArray['code']        = $code;
                $otpArray['expire_date'] = $expire_date;
                $otp                     = Otp::create($otpArray);
                $user->notify(new OneTimePasswordSend($otp->code));

                return response()->json([
                    'status' => 200,
                    'data'   => 'The Code generate successfully',
                ], 200);
            }
        }
    }

    public function verify_otp(Request $request)
    {
        $requestArray = [
            'code' => 'required|string',
        ];
        $validator = Validator::make($request->all(), $requestArray);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 401,
                'message' => $validator->errors(),
            ], 401);
        }

        $otp = Otp::where(['code' => $request->code])->latest('id')->first();
        if (!blank($otp)) {
            $expire_date  = strtotime($otp->expire_date);
            $current_date = strtotime(date('Y-m-d H:i:s'));
            if ($expire_date >= $current_date) {

                $user = $otp->user;
                $shop = !blank($user->shop) ? new ShopResource($user->shop) : [];

                if (!$token = Auth::guard('api')->login($user)) {
                    return response()->json([
                        'data'    => [],
                        'message' => 'You try to using invalid username or password',
                        'status'  => 401,
                    ], 401);
                }
                return (new PrivateUserResource($user))
                    ->additional([
                        'token' => $token,
                        'shop'  => $shop,
                    ]);
            } else {
                return response()->json([
                    'status' => 401,
                    'data'   => 'This code expire time is exceded',
                ], 401);
            }
        }
        return response()->json([
            'status' => 401,
            'data'   => 'The code is not valid.',
        ], 401);
    }

}
