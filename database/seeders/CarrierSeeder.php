<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarrierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = public_path('sql/carriers_data.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

        $pathRelation = public_path('sql/transports_carriers_data.sql');
        $sqlRelation = file_get_contents($pathRelation);
        DB::unprepared($sqlRelation);

        DB::table('carriers')->update([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('transports_carriers')->update([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
