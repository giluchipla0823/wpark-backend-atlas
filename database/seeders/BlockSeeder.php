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
                'name' => 'BLOQUE ZP',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'BLOQUE ZK',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'BLOQUE GLOBAL',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'BLOQUE REVISIÓN',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        ];

        Block::insert($blocks);
    }
}
