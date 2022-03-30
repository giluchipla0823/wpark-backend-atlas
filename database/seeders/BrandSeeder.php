<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Compound;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = [
            [
                'name' => 'FORD',
                'code' => '1',
                'compound_id' => Compound::inRandomOrder()->first()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'UNKNOWN',
                'code' => '999',
                'compound_id' => Compound::inRandomOrder()->first()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        Brand::insert($brands);
    }
}
