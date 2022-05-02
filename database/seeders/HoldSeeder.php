<?php

namespace Database\Seeders;

use App\Models\Hold;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class HoldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $holds = [
            [
                'name' => 'BLOQUEO TEMPORAL',
                'code' => 'BP2',
                'priority' => 2,
                'role_id' => Role::inRandomOrder()->first()->id,
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'PENDIENTE DE REVISAR',
                'code' => 'PDR',
                'priority' => 1,
                'role_id' => null,
                'active' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        Hold::insert($holds);

        // Relacionar condiciones con los holds
        $hold1 = Hold::find(1);
        $hold1->conditions()->sync([1,2]);

        $hold2 = Hold::find(2);
        $hold2->conditions()->sync([1]);
    }
}
