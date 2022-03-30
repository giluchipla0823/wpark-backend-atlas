<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $colors = [
            [
                'name' => 'RUBY RED',
                'code' => 'RYBB',
                'simple_name' => 'RED',
                'hex' => '#9b111e',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'BLUE OCEAN',
                'code' => 'BOCE',
                'simple_name' => 'BLUE',
                'hex' => '#064273',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'GREEN FOREST',
                'code' => 'GRFR',
                'simple_name' => 'GREEN',
                'hex' => '#228b61',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'AGATA BLACK',
                'code' => 'AGBL',
                'simple_name' => 'BLACK',
                'hex' => '#212121',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'SMURF BLUE',
                'code' => 'SMBL',
                'simple_name' => 'BLUE',
                'hex' => '#038edf',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'FURY ORANGE',
                'code' => 'FUGE',
                'simple_name' => 'ORANGE',
                'hex' => '#f88847',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'FROZEN WHITE',
                'code' => 'FRWH',
                'simple_name' => 'WHITE',
                'hex' => '#d7dae3',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        ];

        Color::insert($colors);
    }
}
