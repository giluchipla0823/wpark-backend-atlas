<?php

namespace Database\Seeders;

use App\Models\Dealer;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DealerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dealers = [
            [
                'name' => 'GARATGE CENTRAL, S.A.',
                'code' => '10010',
                'zip_code' => '17600',
                'city' => 'Figueres',
                'street' => 'SANT PAU DE LA CALÇADA,9',
                'country' => 'España',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'TARRACO CENTER, S.A.',
                'code' => '10450',
                'zip_code' => '43006',
                'city' => 'Tarragona',
                'street' => 'C/PLATA,2 (P.I. RIU CLAR)',
                'country' => 'España',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        Dealer::insert($dealers);
    }
}
