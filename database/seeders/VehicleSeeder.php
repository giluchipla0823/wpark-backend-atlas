<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Country;
use App\Models\Design;
use App\Models\DestinationCode;
use App\Models\Movement;
use App\Models\Parking;
use App\Models\Transport;
use App\Models\User;
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
                'vin' => 'NM0GE9E20N1514920',
                'lvin' => 'NM0GE9E20N1514920',
                'vin_short' => 'MK08940',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'destination_code_id' => 1,
                'entry_transport_id' => 1,
                'eoc' => '2FUS K5W3384920 WPGUMK08949 5IG 73UD NVBE KE5R DB 5FEGJD SOEIAC 2P5 BP 0',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vin' => 'NM0GE9E20N1514931',
                'lvin' => 'NM0GE9E20N1514931',
                'vin_short' => 'MK08951',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'destination_code_id' => 1,
                'entry_transport_id' => 1,
                'eoc' => '2FUS K5UO385120 WPGUMK08952 5IG 73UD NVBF KE5R DB 5FABJD SOPI 235 BO 1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vin' => 'NM0GE9E20N1514922',
                'lvin' => 'NM0GE9E20N1514922',
                'vin_short' => 'MK08942',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'destination_code_id' => 2,
                'entry_transport_id' => 1,
                'eoc' => '2FUS K5W3384920 WPGUMK08949 5IG 73UD NVBE KE5R DB 5FEGJD SOEIAC 2P5 BP 2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vin' => 'NM0GE9E20N1514933',
                'lvin' => 'NM0GE9E20N1514933',
                'vin_short' => 'MK08953',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'destination_code_id' => 2,
                'entry_transport_id' => 1,
                'eoc' => '2FUS K5UO385120 WPGUMK08952 5IG 73UD NVBF KE5R DB 5FABJD SOPI 235 BO 3',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vin' => 'NM0GE9E20N1514924',
                'lvin' => 'NM0GE9E20N1514924',
                'vin_short' => 'MK08944',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'destination_code_id' => 3,
                'entry_transport_id' => 1,
                'eoc' => '2FUS K5W3384920 WPGUMK08949 5IG 73UD NVBE KE5R DB 5FEGJD SOEIAC 2P5 BP 4',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vin' => 'NM0GE9E20N1514935',
                'lvin' => 'NM0GE9E20N1514935',
                'vin_short' => 'MK08955',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'destination_code_id' => 3,
                'entry_transport_id' => 1,
                'eoc' => '2FUS K5UO385120 WPGUMK08952 5IG 73UD NVBF KE5R DB 5FABJD SOPI 235 BO 5',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vin' => 'NM0GE9E20N1514926',
                'lvin' => 'NM0GE9E20N1514926',
                'vin_short' => 'MK08946',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'destination_code_id' => 4,
                'entry_transport_id' => 1,
                'eoc' => '2FUS K5W3384920 WPGUMK08949 5IG 73UD NVBE KE5R DB 5FEGJD SOEIAC 2P5 BP 6',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vin' => 'NM0GE9E20N1514937',
                'lvin' => 'NM0GE9E20N1514937',
                'vin_short' => 'MK08957',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'destination_code_id' => 4,
                'entry_transport_id' => 1,
                'eoc' => '2FUS K5UO385120 WPGUMK08952 5IG 73UD NVBF KE5R DB 5FABJD SOPI 235 BO 7',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vin' => 'NM0GE9E20N1514928',
                'lvin' => 'NM0GE9E20N1514928',
                'vin_short' => 'MK08948',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'destination_code_id' => 5,
                'entry_transport_id' => 1,
                'eoc' => '2FUS K5W3384920 WPGUMK08949 5IG 73UD NVBE KE5R DB 5FEGJD SOEIAC 2P5 BP 8',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vin' => 'NM0GE9E20N1514939',
                'lvin' => 'NM0GE9E20N1514939',
                'vin_short' => 'MK08959',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'destination_code_id' => 5,
                'entry_transport_id' => 1,
                'eoc' => '2FUS K5UO385120 WPGUMK08952 5IG 73UD NVBF KE5R DB 5FABJD SOPI 235 BO 9',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vin' => 'NM0GE9E20N1514910',
                'lvin' => 'NM0GE9E20N1514910',
                'vin_short' => 'MK08910',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'destination_code_id' => 2,
                'entry_transport_id' => 1,
                'eoc' => '2FUS K5W3384920 WPGUMK08949 5IG 73UD NVBE KE5R DB 5FEGJD SOEIAC 2P5 B0 E',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'vin' => 'NM0GE9E20N1514912',
                'lvin' => 'NM0GE9E20N1514912',
                'vin_short' => 'MK08912',
                'design_id' => Design::inRandomOrder()->first()->id,
                'color_id' => Color::inRandomOrder()->first()->id,
                'destination_code_id' => 5,
                'entry_transport_id' => 1,
                'eoc' => '2FUS K5UO385120 WPGUMK08952 5IG 73UD NVBF KE5R DB 5FABJD SOPI 235 5O E',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        Vehicle::insert($vehicles);

        // Relacionar etapas con los vehículos
        $vehicle1 = Vehicle::find(1);
        $vehicle1->stages()->sync([
            1 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ],
            2 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ],
            3 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ],
            4 => [
                'manual' => true,
                'tracking_date' => Carbon::now()
            ],
            5 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ]
        ]);

        $vehicle2 = Vehicle::find(2);
        $vehicle2->stages()->sync([
            1 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ]
        ]);

        $vehicle3 = Vehicle::find(3);
        $vehicle3->stages()->sync([
            1 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ]
        ]);

        $vehicle4 = Vehicle::find(4);
        $vehicle4->stages()->sync([
            1 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ]
        ]);

        $vehicle5 = Vehicle::find(5);
        $vehicle5->stages()->sync([
            1 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ]
        ]);

        $vehicle6 = Vehicle::find(6);
        $vehicle6->stages()->sync([
            1 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ]
        ]);

        $vehicle7 = Vehicle::find(7);
        $vehicle7->stages()->sync([
            1 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ]
        ]);

        $vehicle8 = Vehicle::find(8);
        $vehicle8->stages()->sync([
            1 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ]
        ]);

        $vehicle9 = Vehicle::find(9);
        $vehicle9->stages()->sync([
            1 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ]
        ]);

        $vehicle10 = Vehicle::find(10);
        $vehicle10->stages()->sync([
            1 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ]
        ]);

        $vehicle11 = Vehicle::find(11);
        $vehicle11->stages()->sync([
            1 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ]
        ]);

        $vehicle12 = Vehicle::find(12);
        $vehicle12->stages()->sync([
            1 => [
                'manual' => false,
                'tracking_date' => Carbon::now()
            ]
        ]);

        $parking = Parking::find(1);

        // Asignar estados
        $vehicles = Vehicle::all();

        foreach ($vehicles as $vehicle) {
            $vehicle->states()->sync([
                1 => [
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()
                ]
            ]);

            Movement::create([
               "vehicle_id" => $vehicle->id,
               "user_id" => User::inRandomOrder()->first()->id,
                "origin_position_type" => get_class($parking),
                "origin_position_id" => 0,
                "destination_position_type" => get_class($parking),
                "destination_position_id" => $parking->id,
                "category" => null,
                "confirmed" => 1,
                "canceled" => 0,
                "manual" => 1,
                "dt_start" => Carbon::now(),
                "dt_end" => Carbon::now(),
                "comments" => null,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]);
        }
    }
}
