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
                'name' => 'St3',
                'code' => '03',
                'description' => 'Etapa 3 - Creación del vehículo',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'St4',
                'code' => '04',
                'description' => 'Etapa 4 - Actualización del vehículo',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'St5',
                'code' => '05',
                'description' => 'Etapa 5 - Actualización del vehículo',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'St6',
                'code' => '06',
                'description' => 'Etapa 6 - Actualización del vehículo',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'St7',
                'code' => '07',
                'description' => 'Etapa 7 - Gate Release vehículo',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'St8',
                'code' => '08',
                'description' => 'Etapa 8 - Salida del vehículo',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        ];

        Stage::insert($stages);

    }
}
