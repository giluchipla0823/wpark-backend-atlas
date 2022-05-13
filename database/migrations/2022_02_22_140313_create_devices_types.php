<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDevicesTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices_types', function (Blueprint $table) {
            $table->id()->comment('Identificador único del tipo de dispositivo');
            $table->string('name')->comment('Nombre del tipo de dispositivo');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `devices_types` comment 'Tipos de dispositivos para los que está disponible la aplicación. Pueden ser de tipo móvil, pda...'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices_types');
    }
}
