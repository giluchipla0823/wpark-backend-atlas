<?php

namespace Database\Seeders;

use App\Models\Parking;
use App\Models\ParkingType;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParkingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parkings = [
            [
                'name' => 'CANOPY',
                'area_id' => 1,
                'parking_type_id' => ParkingType::TYPE_UNLIMITED,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'BUFFER',
                'area_id' => 4,
                'parking_type_id' => ParkingType::TYPE_UNLIMITED,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'REPARACIÓN',
                'area_id' => 1,
                'parking_type_id' => ParkingType::TYPE_UNLIMITED,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'PINTURA',
                'area_id' => 1,
                'parking_type_id' => ParkingType::TYPE_UNLIMITED,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'LAVADO',
                'area_id' => 1,
                'parking_type_id' => ParkingType::TYPE_UNLIMITED,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'REMODELACIÓN',
                'area_id' => 1,
                'parking_type_id' => ParkingType::TYPE_UNLIMITED,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        ];

        Parking::insert($parkings);
    }
}
