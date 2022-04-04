<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Country;
use App\Models\Design;
use App\Models\DestinationCode;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vehicles = [
            [
                'vin' => 'NM0GE9E20N1514928',
                'vin_short' => 'MK08949',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'country_id' => Country::inRandomOrder()->first()->id,
                'destination_code_id' => DestinationCode::inRandomOrder()->first()->id,
                'eoc' => '2FUS K5W3384920 WPGUMK08949 5IG 73UD NVBE KE5R DB 5FEGJD SOEIAC 2P5 BP E',
                'hybrid' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vin' => 'NM0GE9E20N1514931',
                'vin_short' => 'MK08952',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'country_id' => Country::inRandomOrder()->first()->id,
                'destination_code_id' => DestinationCode::inRandomOrder()->first()->id,
                'eoc' => '2FUS K5UO385120 WPGUMK08952 5IG 73UD NVBF KE5R DB 5FABJD SOPI 235 BO E',
                'hybrid' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        Vehicle::insert($vehicles);

        // Relacionar etapas con los vehículos
        $Vehicle1 = Vehicle::find(1);
        $Vehicle1->stages()->sync([1,2,3,4,5]);

        $Vehicle2 = Vehicle::find(2);
        $Vehicle2->stages()->sync([1]);
    }
}
