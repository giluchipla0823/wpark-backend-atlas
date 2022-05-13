<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateParkingTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parking_types', function (Blueprint $table) {
            $table->id()->comment('Identificador único del tipo de parking');
            $table->string('name')->comment('Nombre del tipo de parking');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `parking_types` comment 'Tipos de parking. Pueden ser de tipo fila, espiga, ilimitado...'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parking_types');
    }
}
