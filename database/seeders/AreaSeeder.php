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
        // Compound::inRandomOrder()->first()->id

        $areas = [
            [
                'name' => 'FACTORY',
                'compound_id' => 1,
                'zone_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'PRESORTING',
                'compound_id' => 1,
                'zone_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'CAMPA GENERAL',
                'compound_id' => 1,
                'zone_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'BUFFER',
                'compound_id' => 1,
                'zone_id' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'OVERFLOW',
                'compound_id' => 1,
                'zone_id' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        Area::insert($areas);
    }
}
