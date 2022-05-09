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
                'name' => 'ANNOUNCED',
                'description' => 'Vehículos anunciados',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ON TERMINAL',
                'description' => 'Vehiculos en terminal',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            /* [
                'name' => 'READY',
                'description' => 'Vehículos en movimiento',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ], */
            [
                'name' => 'LEFT',
                'description' => 'Vehículos ya transportados',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        State::insert($states);

    }
}
