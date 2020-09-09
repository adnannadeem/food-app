<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Enums\UserApplied;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\v1\RegisterResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function action(Request $request)
    {
        $validator = new RegisterRequest();
        $validator = Validator::make($request->all(), $validator->rules());

        if (!$validator->fails()) {
            if ($request->has('name')) {
                $parts      = $this->split_name($request->get('name'));
                $first_name = $parts[0];
                $last_name  = $parts[1];
            }

            if ($request->has('email')) {
                $result   = explode('@', $request->get('email'));
                $username = $result[0];
            }

            $userArray = [
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'email'      => $request->get('email'),
                'username'   => $username,
                'phone'      => $request->get('phone'),
                'roles'      => $request->get('role') ? $request->get('role') : UserRole::CUSTOMER,
                'password'   => bcrypt($request->get('password')),
            ];

            if ($userArray['roles'] == UserRole::DELIVERYBOY) {
                $userArray['status']  = UserStatus::INACTIVE;
                $userArray['applied'] = UserApplied::OWN;
            }

            $user = User::create($userArray);
            if ($user) {
                if (!$token = auth()->guard('api')->attempt($request->only('email', 'password'))) {
                    return response()->json([
                        'data'    => [],
                        'message' => 'You try to using invalid username or password',
                        'status'  => 401,
                    ], 401);
                }
                return (new RegisterResource($user))
                    ->additional([
                        'token' => $token,
                    ]);

            } else {
                return response()->json([
                    'status'  => 400,
                    'message' => 'Bad Request',
                ], 400);
            }
        } else {
            return response()->json([
                'status'  => 200,
                'message' => $validator->errors(),
            ], 200);
        }
    }

    public function split_name($name)
    {
        $name       = trim($name);
        $last_name  = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . $last_name . '#', '', $name));
        return [$first_name, $last_name];
    }
}
