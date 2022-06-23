<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = public_path('sql/designs_data.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

        DB::table('designs')->update([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
