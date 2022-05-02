<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $zones = [
            [
                'name' => 'PLANTA',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'PRESORTING',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'CAMPA GENERAL',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
            ,
            [
                'name' => 'EXTERNO',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
            ,
            [
                'name' => 'OVERFLOW',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        ];

        Zone::insert($zones);
    }
}
