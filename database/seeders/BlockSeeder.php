<?php

namespace Database\Seeders;

use App\Models\Block;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $blocks = [
            [
                'name' => 'BLOQUE PRESORTING',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'BLOQUE POSICIÓN FINAL',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        ];

        Block::insert($blocks);
    }
}
