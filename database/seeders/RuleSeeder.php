<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Condition;
use App\Models\DestinationCode;
use App\Models\Rule;
use App\Models\State;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rules = [
            [
                'name' => 'VEHÍCULOS_ROJOS_Y_AZULES',
                'countdown' => 0,
                'priority' => 2,
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'DESTINO_TARRAGONA',
                'countdown' => 0,
                'priority' => 1,
                'active' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        Rule::insert($rules);

        // TODO: Refactorizar la relacion entre reglas y condiciones
        $rule1 = Rule::find(1);
        $destinationCode = DestinationCode::find(1);
        $color1 = Color::find(1);
        $color2 = Color::find(2);

        $conditions1 = [$destinationCode, $color1, $color2];
        $rulesConditions = [];

        foreach ($conditions1 as $condition) {
            $condition_id = Condition::where('model', get_class($condition))->first();

            $rulesConditions[] = [
                'rule_id' => $rule1->id,
                'condition_id' => $condition_id->id,
                'conditionable_id' => $condition->id,
                'conditionable_type' => get_class($condition),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('rules_conditions')->insert($rulesConditions);
        $rule1->blocks()->sync([1]);

        $rule2 = Rule::find(2);
        $destinationCode = DestinationCode::find(6);

        $conditions2 = [$destinationCode];
        $rulesConditions2 = [];

        foreach ($conditions2 as $condition) {

            $condition_id = Condition::where('model', get_class($condition))->first();

            $rulesConditions2[] = [
                'rule_id' => $rule2->id,
                'condition_id' => $condition_id->id,
                'conditionable_id' => $condition->id,
                'conditionable_type' => get_class($condition),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

        }

        DB::table('rules_conditions')->insert($rulesConditions2);
        $rule2->blocks()->sync([2]);
    }
}
