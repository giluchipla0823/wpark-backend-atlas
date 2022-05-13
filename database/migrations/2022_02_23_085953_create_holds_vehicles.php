<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateHoldsVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holds_vehicles', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la relación');
            $table->foreignId('vehicle_id')->comment('Identificador del vehículo')->constrained('vehicles');
            $table->foreignId('hold_id')->comment('Identificador del bloqueo')->constrained('holds');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `holds_vehicles` comment 'Relación de los bloqueos con los vehículos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('holds_vehicles');
    }
}
