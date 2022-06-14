<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            $table->id()->comment('Identificador único del vehículo');
            $table->string('vin', 17)->unique()->comment('Número de bastidor del vehículo');
            $table->string('lvin', 17)->nullable()->unique()->comment('Número de bastidor lógico del vehículo');
            $table->string('vin_short', 7)->comment('Número de bastidor corto del vehículo');
            $table->foreignId('design_id')->comment('Indica el modelo del vehículo')->constrained('designs');
            $table->foreignId('color_id')->comment('Indica el color del vehículo')->constrained('colors');
            $table->foreignId('destination_code_id')->comment('Indica el código de destino del vehículo')->constrained('destination_codes');
            $table->foreignId('entry_transport_id')->comment('Indica el método de entrada del vehículo')->constrained('transports');
            $table->foreignId('load_id')->nullable()->comment('Indica la carga a la que pertenece el vehículo')->constrained('loads');
            $table->foreignId('route_id')->nullable()->comment('Indicar la ruta por defecto o alternativa seleccionada en la carga')->constrained('routes');
            $table->foreignId('dealer_id')->nullable()->comment('Indica el distribuidor al que irá el vehículo')->constrained('dealers');
            $table->string('eoc')->nullable()->unique()->comment('Identificador único de ford');
            $table->foreignId('last_rule_id')->nullable()->comment('Indica la última regla con mayor prioridad asociada al vehículo')->constrained('rules');
            $table->foreignId('shipping_rule_id')->nullable()->comment('Indica la regla de código de destino asociada al vehículo')->constrained('rules');
            $table->string('info', 100)->nullable()->default('NULL')->comment('Información adicional del vehículo');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `vehicles` comment 'Vehículos que se van a manejar en la campa'");
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
