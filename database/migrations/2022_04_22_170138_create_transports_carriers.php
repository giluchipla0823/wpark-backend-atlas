<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTransportsCarriers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transports_carriers', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la relación');
            $table->foreignId('transport_id')->comment('Identificador del método de transporte')->constrained('transports');
            $table->foreignId('carrier_id')->comment('Identificador de la empresa de transporte')->constrained('carriers');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `transports_carriers` comment 'Relación de los métodos de transporte con las empresas de transporte'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transports_carriers');
    }
}
