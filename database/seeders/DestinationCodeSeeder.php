<?php

namespace Database\Seeders;

use App\Models\DestinationCode;
use App\Models\Route;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DestinationCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $destinationCodes = [
            [
                'name' => 'BAR PORT_SETRAM',
                'code' => 'R6_D',
                'route_id' => Route::inRandomOrder()->first()->id,
                'country_id' => Country::inRandomOrder()->first()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ZD_TARRAGONA',
                'code' => 'ZD',
                'route_id' => Route::inRandomOrder()->first()->id,
                'country_id' => Country::inRandomOrder()->first()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        DestinationCode::insert($destinationCodes);
    }
}
