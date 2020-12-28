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
            'username' => ['required', 'string', 'min:3', 'max:255', new CheckUsernameValidation($request->username)],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'login_type' => ['required', 'numeric', 'gte:0', 'lte:1']
        ]);

        $user = User::where('username', '=', $request->username)
            ->orWhere('email', '=', $request->username)->first();

        $credentials = [
            'email' => $user->email,
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
            'username' => $user->username,
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
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users', 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:255', 'confirmed'],
            'login_type' => ['required', 'numeric', 'gte:0', 'lte:1']
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return $this->login($request);
    }
}
