<?php

namespace Database\Seeders;

use App\Models\ParkingType;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ParkingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parkingTypes = [
            [
                'name' => 'FILA',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'ESPIGA',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'ILIMITADO',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        ];

        parkingType::insert($parkingTypes);
    }
}
