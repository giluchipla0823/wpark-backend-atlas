<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;

class VehicleStates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle:states';

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
//        Vehicle::whereRaw("vehicles.id NOT IN (SELECT vehicle_id FROM vehicles_states)")
//            ->chunk(100, function($vehicles) {
//                foreach ($vehicles as $vehicle) {
//                    $vehicle->states()->sync(1);
//                }
//            });

        $vehicles = Vehicle::whereRaw("vehicles.id NOT IN (SELECT vehicle_id FROM vehicles_stages)")->get();


        foreach ($vehicles as $vehicle) {
            $vehicle->stages()->sync(1);
        }

        return 0;
    }
}
