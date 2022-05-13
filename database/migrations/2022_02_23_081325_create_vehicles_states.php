<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateVehiclesStates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles_states', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la relación');
            $table->foreignId('vehicle_id')->comment('Identificador del vehículo')->constrained('vehicles');
            $table->foreignId('state_id')->comment('Identificador del estado')->constrained('states');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `vehicles_states` comment 'Relación de vehículos con los estados'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles_states');
    }
}
