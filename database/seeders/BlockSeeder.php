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
                'is_presorting' => 1,
                'presorting_default' => 1,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'BLOQUE POSICIÓN FINAL',
                'is_presorting' => 0,
                'presorting_default' => null,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'name' => 'BLOQUE ESPIGA - POSICIÓN FINAL',
                'is_presorting' => 0,
                'presorting_default' => null,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ]
        ];

        Block::insert($blocks);
    }
}
