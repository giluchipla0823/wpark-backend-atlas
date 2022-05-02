<?php

namespace Database\Seeders;

use App\Models\Carrier;
use App\Models\Route;
use App\Models\Compound;
use App\Models\Dealer;
use App\Models\Transport;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

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
                'code' => 'ANTC1',
                'carrier_id' => Carrier::inRandomOrder()->first()->id,
                'exit_transport_id' => Transport::inRandomOrder()->first()->id,
                'origin_compound_id' => Compound::inRandomOrder()->first()->id,
                'destination_compound_id' => Compound::inRandomOrder()->first()->id,
                'dealer_id' => Dealer::inRandomOrder()->first()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'VAL PORT_AMX',
                'code' => 'AMX',
                'carrier_id' => Carrier::inRandomOrder()->first()->id,
                'exit_transport_id' => Transport::inRandomOrder()->first()->id,
                'origin_compound_id' => Compound::inRandomOrder()->first()->id,
                'destination_compound_id' => Compound::inRandomOrder()->first()->id,
                'dealer_id' => Dealer::inRandomOrder()->first()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        Route::insert($routes);
    }
}
