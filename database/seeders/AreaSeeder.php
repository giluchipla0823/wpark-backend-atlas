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
                'compound_id' => Compound::inRandomOrder()->first()->id,
                'zone_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'PRESORTING',
                'compound_id' => Compound::inRandomOrder()->first()->id,
                'zone_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'CAMPA GENERAL',
                'compound_id' => Compound::inRandomOrder()->first()->id,
                'zone_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        Area::insert($areas);
    }
}
