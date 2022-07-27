<?php

namespace App\Console\Commands;

use App\Models\Movement;
use App\Models\Parking;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;

class VehicleMovement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle:movement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $model = new Vehicle();

        $model->query()->chunk(100, function($vehicles) {
            foreach ($vehicles as $vehicle) {
                Movement::create([
                    'vehicle_id' => $vehicle->id,
                    'user_id' => 1,
                    'device_id' => null,
                    'origin_position_id' => 0,
                    'origin_position_type' => Parking::class,
                    'destination_position_id' => 1,
                    'destination_position_type' => Parking::class,
                    'confirmed' => 1,
                    'canceled' => 0,
                    'manual' => 0,
                    'dt_start' => Carbon::now(),
                    'dt_end' => Carbon::now(),
                    'category' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        });


        return 0;
    }
}
