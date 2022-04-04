<?php

namespace Database\Seeders;

use App\Models\Stage;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stages = [
            [
                'name' => 'STAGE 3',
                'short_name' => 'ST3',
                'description' => 'Etapa 3 - Creación del vehículo',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'STAGE 4',
                'short_name' => 'ST4',
                'description' => 'Etapa 4 - Actualización del vehículo',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'STAGE 5',
                'short_name' => 'ST5',
                'description' => 'Etapa 5 - Actualización del vehículo',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'STAGE 6',
                'short_name' => 'ST6',
                'description' => 'Etapa 6 - Actualización del vehículo',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'STAGE 7',
                'short_name' => 'ST7',
                'description' => 'Etapa 7 - Salida del vehículo',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'STAGE 8',
                'short_name' => 'ST8',
                'description' => 'Etapa 8 - Actualización del vehículo',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        ];

        Stage::insert($stages);

    }
}
