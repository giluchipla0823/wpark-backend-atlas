<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            // CompoundSeeder::class,
            CompoundSqlSeeder::class,
            BrandSeeder::class,
            ColorSeeder::class,
            DesignSeeder::class,
            CountrySeeder::class,
            TransportSeeder::class,
            CarrierSeeder::class,
            DealerSeeder::class,
            DeviceTypeSeeder::class,
            DeviceSeeder::class,
            DestinationCodeSeeder::class,
            RouteTypeSeeder::class,
            RouteSeeder::class,
            ModelConditionSeeder::class,
            ConditionSeeder::class,
            StateSeeder::class,
            BlockSeeder::class,
            ZoneSeeder::class,
            AreaSeeder::class,
            ParkingTypeSeeder::class,
            // ParkingSeeder::class,
            ParkingSqlSeeder::class,
            UserSeeder::class,
            PermissionSeeder::class,
            HoldSeeder::class,
            StageSeeder::class,
            // RuleSeeder::class,
            RuleSqlSeeder::class,
            VehicleSqlSeeder::class,
            // VehicleSeeder::class
            MovementSqlSeeder::class,
            NotificationSqlSeeder::class,
            RecirculationSqlSeeder::class,

        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
