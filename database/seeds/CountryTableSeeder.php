<?php

use App\Country;
use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::truncate();
        Country::flushEventListeners();

        Country::create([
            'name' => 'Indonesia',
        ]);
        Country::create([
            'name' => 'Singapore',
        ]);
    }
}
