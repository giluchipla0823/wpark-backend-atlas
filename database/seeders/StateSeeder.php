<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = [
            [
                'name' => 'ON TERMINAL',
                'description' => 'Vehículos que en terminal',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'READY',
                'description' => 'Vehiculos que están preparados',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'LEFT',
                'description' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ON_ROUTE',
                'description' => 'Vehículos en ruta',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        State::insert($states);

    }
}
