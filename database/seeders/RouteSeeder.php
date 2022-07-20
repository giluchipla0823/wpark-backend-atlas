<?php

namespace Database\Seeders;

use App\Models\Carrier;
use App\Models\Route;
use App\Models\Compound;
use App\Models\DestinationCode;
use App\Models\RouteType;
use App\Models\Transport;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = [
            [
                'name' => 'ANTWERP CHINA',
                'cdm_code' => 'ANTC1',
                'route_type_id' => RouteType::inRandomOrder()->first()->id,
                'carrier_id' => Carrier::inRandomOrder()->first()->id,
                'destination_code_id' => DestinationCode::inRandomOrder()->first()->id,
                'origin_compound_id' => Compound::inRandomOrder()->first()->id,
                'destination_compound_id' => Compound::inRandomOrder()->first()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'VAL PORT_AMX',
                'cdm_code' => 'AMX',
                'route_type_id' => RouteType::inRandomOrder()->first()->id,
                'carrier_id' => Carrier::inRandomOrder()->first()->id,
                'destination_code_id' => DestinationCode::inRandomOrder()->first()->id,
                'origin_compound_id' => Compound::inRandomOrder()->first()->id,
                'destination_compound_id' => Compound::inRandomOrder()->first()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        // Route::insert($routes);

        $path = public_path('sql/routes_data.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
