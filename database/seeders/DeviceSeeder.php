<?php

namespace Database\Seeders;

use App\Models\Dealer;
use App\Models\Device;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DeviceSeeder extends Seeder
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
                'name' => 'Device 1',
                'uuid' => '123456',
                'version' => '1.0.0',
                'device_type_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Device 2',
                'uuid' => '987654',
                'version' => '1.0.0',
                'device_type_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Device 3',
                'uuid' => '765123',
                'version' => '1.0.0',
                'device_type_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        Device::insert($data);
    }
}
