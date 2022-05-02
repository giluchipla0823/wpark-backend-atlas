<?php

namespace Database\Seeders;

use App\Models\Carrier;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CarrierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $carriers = [
            [
                'name' => 'TRANSFESA',
                'code' => 'TRANS',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'CAPSA',
                'code' => 'CPS',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        Carrier::insert($carriers);
    }
}
