<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Condition;
use App\Models\Design;
use App\Models\DestinationCode;
use App\Models\Stage;
use App\Models\Vehicle;
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
                'name' => 'VIN',
                'description' => 'Regla por vin',
                'model' => Vehicle::class,
                'model_condition_id' => 2,
                'required' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ESTACIÓN',
                'description' => 'Regla por estación',
                'model' => Stage::class,
                'model_condition_id' => 2,
                'required' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'MODELO',
                'description' => 'Regla por modelo',
                'model' => Design::class,
                'model_condition_id' => 2,
                'required' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'COLOR',
                'description' => 'Regla por color',
                'model' => Color::class,
                'model_condition_id' => 2,
                'required' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'CÓDIGO DE DESTINO',
                'description' => 'Regla por código de destino',
                'model' => DestinationCode::class,
                'model_condition_id' => 2,
                'required' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'PLANNABLE',
                'description' => 'Plannable',
                'model' => null,
                'model_condition_id' => 1,
                'required' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'TOTAL_STOP',
                'description' => 'Total stop',
                'model' => null,
                'model_condition_id' => 1,
                'required' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'DISPATCH_STOP',
                'description' => 'Dispatch stop',
                'model' => null,
                'model_condition_id' => 1,
                'required' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'NO_RELEASE',
                'description' => 'No Release',
                'model' => null,
                'model_condition_id' => 1,
                'required' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]

        ];

        Condition::insert($conditions);
    }
}
