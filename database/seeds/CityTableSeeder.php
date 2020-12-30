<?php

use App\City;
use Illuminate\Database\Seeder;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::truncate();
        City::flushEventListeners();

        City::create([
            'country_id' => 1,
            'name' => 'Surabaya'
        ]);
        City::create([
            'country_id' => 1,
            'name' => 'Jakarta'
        ]);
        City::create([
            'country_id' => 1,
            'name' => 'Malang'
        ]);
        City::create([
            'country_id' => 2,
            'name' => 'Anson'
        ]);
        City::create([
            'country_id' => 2,
            'name' => 'Kallang Way'
        ]);
        City::create([
            'country_id' => 2,
            'name' => 'Maxwell'
        ]);
    }
}
