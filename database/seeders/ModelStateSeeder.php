<?php

namespace Database\Seeders;

use App\Models\ModelState;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ModelStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modelState = [
            [
                'name' => 'VEHICLE',
                'model' => 'App\Models\Vehicle',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ROW',
                'model' => 'App\Models\Row',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        ModelState::insert($modelState);
    }
}
