<?php

namespace Database\Seeders;

use App\Models\RouteType;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RouteTypeSeeder extends Seeder
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
                'name' => 'DEFAULT',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'ALTERNATIVE',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'EXCEPTION',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        RouteType::insert($data);
    }
}
