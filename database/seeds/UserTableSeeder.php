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
            'email' => 'harismiftahulhudha@gmail.com',
            'username' => 'admin',
            'level' => User::ADMIN,
            'password' => '$2y$10$fGFU9XlvZjmaM4eqqvCMTOO.Rs.034BG6QJsIqnQR5uKByRG0RY02'
        ]);
    }
}
