<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTransports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transports', function (Blueprint $table) {
            $table->id()->comment('Identificador único del método de transporte');
            $table->string('name')->comment('Nombre del método de transporte');
            $table->boolean('active')->default('1')->comment('Indica si el transporte está activo (0: No esta activo, 1: Esta activo)');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `transports` comment 'Método de transporte para identificar como entra o sale un vehículo de la campa'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transports');
    }
}
