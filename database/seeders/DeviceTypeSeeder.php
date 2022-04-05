<?php

namespace Database\Seeders;

use App\Models\Dealer;
use App\Models\DeviceType;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DeviceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'MOBILE',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'PDA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        DeviceType::insert($data);
    }
}
