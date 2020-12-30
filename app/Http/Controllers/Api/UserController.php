<?php

namespace App\Http\Controllers\Api;

use App\City;
use App\Country;
use App\Http\Controllers\ApiController;
use App\User;
use App\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends ApiController
{
    public function index()
    {
        $db = DB::table('users');
        $db->join('countries', 'users.country_id', 'countries.id');
        $db->join('cities', 'users.city_id', 'cities.id');
        $db->select('users.id', 'users.first_name', 'users.last_name', DB::raw('CONCAT(users.first_name, \' \', users.last_name) as fullname'), 'users.email', 'users.phone',
        'users.birthday', DB::raw('DATE_FORMAT(users.birthday, \'%d/%m/%Y\') as format_birthday'), 'users.country_id', 'countries.name as country_name', 'users.city_id', 'cities.name as city_name',
        'users.level', 'users.photo', 'users.created_at', 'users.updated_at');
        if (request()->has('query')) {
            $q = request()->get('query');
            $db->where('users.first_name', 'LIKE', '%' . $q . '%')
                ->orWhere('users.last_name', 'LIKE', '%' . $q . '%');
        }
        if (request()->has('country')) {
            $q = request()->get('country');
            $db->where('users.country_id', $q);
        }
        if (request()->has('city')) {
            $q = request()->get('city');
            $db->where('users.city_id', $q);
        }
        $users = $db->get();
        return $this->showAll($users);
    }

    public function store(Request  $request)
    {
        $this->validate($request, [
            'first_name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['required', 'string', 'min:3', 'max:255', 'unique:users'],
            'birthday' => ['required', 'date'],
            'country_id' => ['required', 'exists:countries,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:255', 'confirmed'],
            'photo' => ['sometimes', 'mimes:jpg,jpeg,png', 'max:1000'],
            'is_admin' => ['required', 'numeric', 'gte:0', 'lte:1']
        ]);

        $path = null;
        if ($request->hasFile('photo')) {
            $filename = 'user-' . preg_replace("/\s+/", "", strtolower($request->first_name)) . '-' . time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $path = Storage::disk('public')->putFileAs('images', $request->file('photo'), $filename);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'birthday' => $request->birthday,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'password' => bcrypt($request->password),
            'level' => $request->is_admin,
            'photo' => $path
        ]);

        $user->full_name = $user->first_name . ' ' . $user->last_name;
        $user->country_name = Country::find($user->country_id)->name;
        $user->city_name = City::find($user->city_id)->name;
        $user->format_birthday = date('d/m/Y', strtotime($user->birthday));

        return $this->showData($user);
    }

    public function show($id)
    {
        $db = DB::table('users');
        $db->join('countries', 'users.country_id', 'countries.id');
        $db->join('cities', 'users.city_id', 'cities.id');
        $db->select('users.id', 'users.first_name', 'users.last_name', DB::raw('CONCAT(users.first_name, \' \', users.last_name) as fullname'), 'users.email', 'users.phone',
            'users.birthday', DB::raw('DATE_FORMAT(users.birthday, \'%d/%m/%Y\') as format_birthday'), 'users.country_id', 'countries.name as country_name', 'users.city_id', 'cities.name as city_name',
            'users.level', 'users.photo', 'users.created_at', 'users.updated_at');
        $db->where('users.id', $id);
        $user = $db->first();

        return $this->showData($user);
    }

    public function update(Request  $request, $id)
    {
        $user = User::find($id);
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
            'is_admin' => ['required', 'numeric', 'gte:0', 'lte:1'],
            'is_delete_photo' => ['required', 'numeric', 'gte:0', 'lte:1']
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->birthday = $request->birthday;
        $user->country_id = $request->country_id;
        $user->city_id = $request->city_id;
        $user->email = $request->email;
        $user->level = $request->is_admin;

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

        $user->full_name = $user->first_name . ' ' . $user->last_name;
        $user->country_name = Country::find($user->country_id)->name;
        $user->city_name = City::find($user->city_id)->name;
        $user->format_birthday = date('d/m/Y', strtotime($user->birthday));

        return $this->showData($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if ($user->photo != null) {
            Storage::delete($user->photo);
        }
        $user->delete();

        return $this->showData($user);
    }
}
