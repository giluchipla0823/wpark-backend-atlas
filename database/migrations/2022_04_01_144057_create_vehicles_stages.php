<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateVehiclesStages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles_stages', function (Blueprint $table) {
            $table->id()->comment('Identificador de la relación');
            $table->foreignId('vehicle_id')->comment('Identificador del vehículo')->constrained('vehicles');
            $table->foreignId('stage_id')->comment('Identificador de la estación')->constrained('stages');
            $table->boolean('manual')->default('0')->comment('Indica si Ford ha mandado la información de forma manual o automática (0: Automática, 1: Manual)');
            $table->datetime('tracking_date')->nullable()->comment('Fecha del cambio de estación que recibimos de la api de Ford');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `vehicles_stages` comment 'Relación de los vehículos con las estaciones'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles_stages');
    }
}
