<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PasswordUpdateRequest;
use App\Http\Requests\Api\ProfileUpdateRequest;
use App\Http\Resources\v1\MeResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class MeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api'])->except('refresh');
    }

    public function action(Request $request)
    {
        return new MeResource($request->user());
    }

    public function refresh()
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            throw new BadRequestHttpException('Token not provided');
        }

        try {
            $token = JWTAuth::refresh($token);
        } catch (TokenInvalidException $e) {
            throw new AccessDeniedException('The token is invalid');
        }

        return response()->json([
            'success'    => true,
            'token'      => $token,
            "token_type" => "bearer",
            'expires_in' => config('jwt.ttl') * 3600000000000,
        ], 200);

    }

    public function update(Request $request)
    {
        $profile = User::where(['id' => auth()->user()->id])->first();
        if (!blank($profile)) {
            $validator = new ProfileUpdateRequest($profile->id);
            $validator = Validator::make($request->all(), $validator->rules());
            if (!$validator->fails()) {
                $firstName = '';
                $lastName  = '';
                if ($request->has('name')) {
                    $parts     = $this->splitName($request->get('name'));
                    $firstName = $parts[0];
                    $lastName  = $parts[1];
                }

                $profile->first_name = $firstName;
                $profile->last_name  = $lastName;
                $profile->email      = $request->get('email');
                $profile->phone      = $request->get('phone');
                $profile->address    = $request->get('address');
                if ($request->username) {
                    $profile->username = $request->username;
                }

                $profile->save();
                if ($request->get('image') != '') {

                    $realImage = base64_decode($request->get('image'));
                    file_put_contents($request->get('fileName'), $realImage);

                    $url = public_path($request->get('fileName'));

                    $profile->media()->delete();
                    $profile->addMediaFromUrl($url)->toMediaCollection('user');

                    File::delete($url);
                }

                return response()->json([
                    'status'  => 200,
                    'message' => 'Successfully Updated Profile',
                ], 200);
            } else {
                return response()->json([
                    'status'  => 200,
                    'message' => $validator->errors(),
                ], 200);
            }
        }
    }

    private function splitName($name)
    {
        $name       = trim($name);
        $last_name  = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . $last_name . '#', '', $name));
        return [$first_name, $last_name];
    }

    public function changePassword(Request $request)
    {
        $validator = new PasswordUpdateRequest();
        $validator = Validator::make($request->all(), $validator->rules());

        if (!$validator->fails()) {
            $profile           = User::where(['id' => auth()->user()->id])->first();
            $profile->password = bcrypt($request->get('password'));
            $profile->save();
            return response()->json([
                'status'  => 200,
                'message' => 'Successfully Updated Password',
            ], 200);
        } else {
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors(),
            ], 422);
        }
    }

    public function device(Request $request)
    {
        $validator = Validator::make($request->all(), ['device_token' => 'required']);

        if (!$validator->fails()) {
            $user               = User::where(['id' => auth()->user()->id])->first();
            $user->device_token = $request->device_token;
            $user->save();
            return response()->json([
                'status'  => 200,
                'message' => 'Successfully device updated',
            ], 200);
        } else {
            return response()->json([
                'status'  => 422,
                'message' => $validator->errors(),
            ], 422);
        }
    }

}
