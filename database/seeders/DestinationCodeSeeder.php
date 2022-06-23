<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DestinationCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = public_path('sql/destination_codes_data.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

        DB::table('destination_codes')->update([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
