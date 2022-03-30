<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id(); // Viendo la tarea del servicio del detalle del vehículo faltaría por mostrar marca, dtGate, categoría e info
            $table->string('vin', 17)->unique(); // Definir tamaño 17¿?
            $table->string('vin_short', 10); // Definir tamaño 7¿?
            $table->foreignId('design_id')->constrained('designs');
            $table->foreignId('color_id')->nullable()->constrained('colors');
            $table->foreignId('country_id')->constrained('countries');
            $table->foreignId('destination_code_id')->nullable()->constrained('destination_codes');
            $table->foreignId('slot_id')->constrained('slots')->nullable();
            $table->foreignId('last_slot_id')->constrained('slots')->nullable();
            $table->foreignId('compound_id')->constrained('compounds');
            $table->string('eoc')->unique();
            $table->foreignId('last_rule_id')->constrained('rules');
            $table->foreignId('shipping_rule_id')->constrained('rules');
            $table->string('route_to', 100)->nullable()->default('NULL');
            //$table->datetime('dt_onterminal')->nullable();
            //$table->datetime('dt_left')->nullable();
            $table->foreignId('load_id')->nullable()->constrained('loads');
            //$table->boolean('on_route')->default('0');
            $table->boolean('hybrid')->default('0'); // Preguntar sobre este campo como se maneja
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
