<?php

use App\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        User::flushEventListeners();

        User::create([
            'first_name' => 'admin',
            'last_name' => 'h3h3',
            'email' => 'harismiftahulhudha@gmail.com',
            'phone' => '087855736502',
            'birthday' => '1995-12-10',
            'country_id' => 1,
            'city_id' => 1,
            'level' => User::ADMIN,
            'password' => '$2y$10$fGFU9XlvZjmaM4eqqvCMTOO.Rs.034BG6QJsIqnQR5uKByRG0RY02'
        ]);
    }
}
