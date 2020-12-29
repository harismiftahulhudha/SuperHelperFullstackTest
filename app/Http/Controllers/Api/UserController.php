<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends ApiController
{
    public function index()
    {
        $db = DB::table('users');
        if (request()->has('query')) {
            $q = request()->get('query');
            $db->where('first_name', 'LIKE', '%' . $q . '%')
                ->orWhere('first_name', 'LIKE', '%' . $q . '%');
        }
        if (request()->has('country')) {
            $q = request()->get('country');
            $db->where('country_id', $q);
        }
        if (request()->has('city')) {
            $q = request()->get('city');
            $db->where('city_id', $q);
        }
        $users = $db->get();
        return $this->showAll($users);
    }
}
