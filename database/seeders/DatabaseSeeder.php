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
            DesignSeeder::class,
            CountrySeeder::class,
            CarrierSeeder::class,
            DealerSeeder::class,
            RouteSeeder::class,
            DestinationCodeSeeder::class,
            ModelConditionSeeder::class,
            ConditionSeeder::class,
            ModelStateSeeder::class,
            StateSeeder::class,
            HoldSeeder::class,
            BlockSeeder::class,
            ZoneSeeder::class,
            AreaSeeder::class,
            parkingTypeSeeder::class,
            ParkingSeeder::class,
            ColorSeeder::class,
            UserSeeder::class,
            PermissionSeeder::class,
            StageSeeder::class,
            VehicleSeeder::class
        ]);
    }
}
