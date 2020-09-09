<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\v1\PrivateUserResource;
use App\Http\Resources\v1\ShopResource;
use App\User;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['action']]);
    }

    public function action(LoginRequest $request)
    {
        if (!$token = auth()->guard('api')->attempt($request->only('email', 'password'))) {
            return response()->json([
                'data'    => [],
                'message' => 'You try to using invalid username or password',
                'status'  => 401,
            ], 401);
        }

        $user     = $request->user('api');
        $role     = $request->role;
        $mainuser = User::find($user->id);
        $shop     = !blank($mainuser->shop) ? new ShopResource($mainuser->shop) : [];

        if ($role != '') {
            if ($role == $mainuser->roles) {
                return (new PrivateUserResource($mainuser))
                    ->additional([
                        'token' => $token,
                        'shop'  => $shop,
                    ]);
            }
            return response()->json([
                'data'    => [],
                'message' => "You don't have permission to login",
                'status'  => 422,
            ], 422);
        }
        return (new PrivateUserResource($mainuser))
            ->additional([
                'token' => $token,
                'shop'  => $shop,
            ]);
    }

}
