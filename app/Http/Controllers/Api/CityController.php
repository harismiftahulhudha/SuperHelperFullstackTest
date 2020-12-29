<?php

namespace App\Http\Controllers\Api;

use App\City;
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
        return $this->showAll($cities);
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

        return $this->showData($city);
    }

    public function show($id)
    {
        return $this->showData(City::find($id));
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

        return $this->showData($city);
    }

    public function destroy($id)
    {
        $city = City::find($id);
        $city->delete();

        return $this->showData($city);
    }
}
