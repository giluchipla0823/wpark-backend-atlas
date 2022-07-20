<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateLoads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loads', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la carga');
            $table->string('transport_identifier', 50)->unique()->comment('Código de la carga/albarán');
            $table->string('license_plate', 50)->comment('Matrícula principal del método de transporte');
            $table->string('trailer_license_plate', 25)->nullable()->comment('Matrícula del remolque del método de transporte');
            $table->string('category')->nullable()->comment('Nombre de la categoría (regla) de la fila a la que pertenecen los vehículos que van en la carga.');
            $table->foreignId('carrier_id')->comment('Indica la empresa de transporte que realiza la carga')->constrained('carriers');
            $table->foreignId('exit_transport_id')->comment('Indica el medio de transporte de salida de los vehículos')->constrained('transports');
            $table->foreignId('compound_id')->comment('Indica la campa donde se realiza la carga')->constrained('compounds');
            $table->boolean('ready')->default('0')->comment('Indica si la carga está preprada (0: No está preparada, 1: Está preparada)');
            $table->boolean('processed')->default('0')->comment('Indica si la carga ya se ha realizado (0: No se ha realizado, 1: Se ha realizado)');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `loads` comment 'Carga de los vehículos con la información necesaria para realizar el albarán de salida'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loads');
    }
}
