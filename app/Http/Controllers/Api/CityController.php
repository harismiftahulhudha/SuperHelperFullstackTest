<?php

namespace App\Http\Controllers\Api;

use App\City;
use App\Country;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends ApiController
{
    public function index()
    {
        $db = DB::table('cities');
        $db->join('countries', 'cities.country_id', '=', 'countries.id')
            ->select('cities.id', 'cities.country_id', 'countries.name as country_name', 'cities.name', 'cities.created_at', 'cities.updated_at');
        if (request()->has('query')) {
            $q = request()->get('query');
            $db->where('cities.name', 'LIKE', '%' . $q . '%');
        }

        $cities = $db->get();
        if (request()->has('noPagination')) {
            return $this->showData($cities);
        } else {
            return $this->showAll($cities);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'country_id' => ['required', 'exists:countries,id'],
        ]);

        $city = City::create([
            'name' => $request->name,
            'country_id' => $request->country_id
        ]);

        $country = Country::find($request->country_id);
        $city->country_name = $country->name;

        return $this->showData($city);
    }

    public function show($id)
    {
        $db = DB::table('cities');
        $db->join('countries', 'cities.country_id', '=', 'countries.id')
            ->select('cities.id', 'cities.country_id', 'countries.name as country_name', 'cities.name', 'cities.created_at', 'cities.updated_at');
        $db->where('cities.id', $id);
        $city = $db->first();
        return $this->showData($city);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'country_id' => ['required', 'exists:countries,id'],
        ]);

        $city = City::find($id);
        $city->name = $request->name;
        $city->country_id = $request->country_id;
        $city->save();

        $country = Country::find($request->country_id);
        $city->country_name = $country->name;

        return $this->showData($city);
    }

    public function destroy($id)
    {
        $city = City::find($id);
        $city->delete();

        return $this->showData($city);
    }
}
