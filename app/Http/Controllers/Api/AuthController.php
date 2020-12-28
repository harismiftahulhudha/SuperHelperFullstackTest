<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Rules\CheckUsernameValidation;
use App\User;
use App\UserToken;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends ApiController
{
    public function login(Request $request) {
        $this->validate($request, [
            'email' => ['required', 'string', 'email', 'min:3', 'max:255', new CheckUsernameValidation($request->email)],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'login_type' => ['required', 'numeric', 'gte:0', 'lte:1']
        ]);

        $user = User::where('email', '=', $request->email)->first();

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse('email atau password anda salah', 400);
            }
        } catch (JWTException $e) {
            return $this->errorResponse('could_not_create_token', 500);
        }

        $loginType = $request->login_type;

        $loginType = $this->checkLoginType($loginType);
        if ($loginType == -1) {
            return $this->errorResponse('Authorization Token not found', 401);
        }

        $loginType = $request->login_type;

        $userToken = UserToken::where('user_id', '=', $user->id)->where('login_type', $loginType)->get();

        $token = JWTAuth::fromUser($user);
        if (count($userToken) == 0) {
            UserToken::create([
                'user_id' => $user->id,
                'token' => $token,
                'login_type' => $loginType
            ]);
        } else {
            UserToken::where('user_id', '=', $user->id)->where('login_type', '=', $loginType)->delete();
            UserToken::create([
                'user_id' => $user->id,
                'token' => $token,
                'login_type' => $loginType
            ]);
        }

        $data = [
            'id' => $user->id,
            'email' => $user->email,
            'token' => $token,
            'level' => $user->level
        ];

        return $this->showData($data);
    }

    public function logout()
    {
        $loginType = request()->header('login-type');
        $splitToken = explode(' ', request()->header('Authorization'));

        $token = UserToken::where('login_type', '=', $loginType)->where('token', '=', $splitToken[1])->get();
        if (count($token) == 0) {
            return $this->errorResponse('Authorization Token not found', 401);
        }
        UserToken::where('login_type', '=', $loginType)->where('token', '=', $splitToken[1])->delete();
        return $this->showMessage('Successfully Logged out', 200);
    }

    public function register(Request $request) {
        $this->validate($request, [
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['required', 'string', 'min:3', 'max:255', 'unique:users'],
            'birthday' => ['required', 'date'],
            'country_id' => ['required', 'exists:countries,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:255', 'confirmed'],
            'login_type' => ['required', 'numeric', 'gte:0', 'lte:1']
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'birthday' => $request->birthday,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'password' => bcrypt($request->password)
        ]);

        return $this->login($request);
    }
}
