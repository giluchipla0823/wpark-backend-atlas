<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DealerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = public_path('sql/dealers_data.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

        DB::table('dealers')->update([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
