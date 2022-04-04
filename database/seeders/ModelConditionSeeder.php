<?php

namespace Database\Seeders;

use App\Models\ModelCondition;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ModelConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modelCondition = [
            [
                'name' => 'HOLD',
                'model' => 'App\Models\Hold',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'RULE',
                'model' => 'App\Models\Rule',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        ModelCondition::insert($modelCondition);
    }
}
