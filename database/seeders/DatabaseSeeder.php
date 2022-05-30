<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CompoundSeeder::class,
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
            parkingTypeSeeder::class,
            ParkingSeeder::class,
            UserSeeder::class,
            PermissionSeeder::class,
            HoldSeeder::class,
            StageSeeder::class,
            //RuleSeeder::class,
            VehicleSeeder::class
        ]);
    }
}
