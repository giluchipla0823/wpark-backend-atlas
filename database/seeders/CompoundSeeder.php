<?php

namespace Database\Seeders;

use App\Models\Compound;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CompoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $compounds = [
            [
                'name' => 'FORD VALENCIA',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'FORD ROMA',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        ];

        Compound::insert($compounds);
    }
}
