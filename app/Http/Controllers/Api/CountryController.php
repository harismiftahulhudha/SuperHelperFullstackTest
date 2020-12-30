<?php

namespace App\Http\Controllers\Api;

use App\Country;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends ApiController
{
    public function index()
    {
        $db = DB::table('countries');
        if (request()->has('query')) {
            $q = request()->get('query');
            $db->where('name', 'LIKE', '%' . $q . '%');
        }

        $countries = $db->get();
        if (request()->has('noPagination')) {
            return $this->showData($countries);
        } else {
            return $this->showAll($countries);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:3', 'max:255']
        ]);

        $country = Country::create([
            'name' => $request->name
        ]);

        return $this->showData($country);
    }

    public function show($id)
    {
        return $this->showData(Country::find($id));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:3', 'max:255']
        ]);

        $country = Country::find($id);
        $country->name = $request->name;
        $country->save();

        return $this->showData($country);
    }

    public function destroy($id)
    {
        $country = Country::find($id);
        $country->delete();

        return $this->showData($country);
    }
}
