<?php

namespace Database\Seeders;

use App\Models\Condition;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conditions = [
            [
                'name' => 'PLANNABLE',
                'description' => 'Plannable',
                'model_condition_id' => 1,
                'required' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'TOTAL_STOP',
                'description' => 'Total stop',
                'model_condition_id' => 1,
                'required' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'CÓDIGO DE DESTINO',
                'description' => 'Regla por código de destino',
                'model_condition_id' => 2,
                'required' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'VIN',
                'description' => 'Regla por vin',
                'model_condition_id' => 2,
                'required' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        Condition::insert($conditions);
    }
}
