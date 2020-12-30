<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends ApiController
{
    public function profile()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user->country;
        $user->city;
        return $this->showData($user);
    }

    public function update(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $this->validate($request, [
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['required', 'string', 'min:3', 'max:255', 'unique:users,phone,' . $user->id],
            'birthday' => ['required', 'date'],
            'country_id' => ['required', 'exists:countries,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', 'string', 'min:6', 'max:255'],
            'photo' => ['sometimes', 'mimes:jpg,jpeg,png', 'max:1000'],
            'is_delete_photo' => ['required', 'numeric', 'gte:0', 'lte:1']
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->birthday = $request->birthday;
        $user->country_id = $request->country_id;
        $user->city_id = $request->city_id;
        $user->email = $request->email;

        if ($request->has('password')) {
            if ($request->password !== '') {
                $user->password = bcrypt($request->password);
            }
        }

        if ($request->hasFile('photo')) {
            if ($user->photo != null) {
                Storage::delete($user->photo);
            }

            $filename = 'user-' . $user->id . '-' . time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $path = Storage::disk('public')->putFileAs('images', $request->file('photo'), $filename);
            $user->photo = $path;
        }

        if ($request->is_delete_photo == 1) {
            if ($user->photo != null) {
                Storage::delete($user->photo);
            }
            $user->photo = null;
        }

        $user->save();
        $user->country;
        $user->city;

        return $this->showData($user);
    }
}
