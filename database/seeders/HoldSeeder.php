<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HoldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = public_path('sql/holds_data.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

        DB::table('holds')->update([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('holds_conditions')->update([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
