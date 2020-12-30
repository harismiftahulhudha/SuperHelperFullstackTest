<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('api.')->group(function () {
    Route::post('login', 'Api\AuthController@login')->name('auth.login');
    Route::post('register', 'Api\AuthController@register')->name('auth.register');

    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::get('logout', 'Api\AuthController@logout')->name('auth.logout');

        Route::get('profile', 'Api\ProfileController@profile')->name('profile');
        Route::put('profile/update', 'Api\ProfileController@update')->name('profile.update');

        Route::resource('users', 'Api\UserController', ['only' => ['index', 'show']]);
        Route::resource('countries', 'Api\CountryController', ['except' => ['create', 'edit']]);
        Route::resource('cities', 'Api\CityController', ['except' => ['create', 'edit']]);

        Route::group(['middleware' => 'admin'], function () {
            Route::resource('users', 'Api\UserController', ['only' => ['store', 'update', 'destroy']]);
        });
    });
});
