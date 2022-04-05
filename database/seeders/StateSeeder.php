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
                'description' => 'Vehículos que están en la campa',
                'model_state_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'READY',
                'description' => 'Vehiculos que están en movimiento',
                'model_state_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'DISPONIBLE',
                'description' => 'Fila disponible',
                'model_state_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'CLOSED',
                'description' => 'Fila cerrada',
                'model_state_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        State::insert($states);

    }
}
