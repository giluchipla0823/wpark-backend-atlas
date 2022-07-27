<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Compound;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $areas = [
            [
                'name' => 'FACTORY',
                'compound_id' => Compound::VALENCIA_ID,
                'zone_id' => Zone::PLANTA,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'PRESORTING',
                'compound_id' => Compound::VALENCIA_ID,
                'zone_id' => Zone::PRESORTING,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'CAMPA GENERAL',
                'compound_id' => Compound::VALENCIA_ID,
                'zone_id' => Zone::CAMPA_GENERAL,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'BUFFER',
                'compound_id' => Compound::VALENCIA_ID,
                'zone_id' => Zone::BUFFER,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'OVERFLOW',
                'compound_id' => Compound::VALENCIA_ID,
                'zone_id' => Zone::OVERFLOW,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        Area::insert($areas);
    }
}
