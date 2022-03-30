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
            Design::class,
            BlockSeeder::class,
            ZoneSeeder::class,
            AreaSeeder::class,
            parkingTypeSeeder::class,
            ParkingSeeder::class,
            ColorSeeder::class,
            UserSeeder::class,
            PermissionSeeder::class
        ]);
    }
}
