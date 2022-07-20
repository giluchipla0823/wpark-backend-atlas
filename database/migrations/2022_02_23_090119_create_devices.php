<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id()->comment('Identificador único del dispositivo');
            $table->string('name')->comment('Nombre del dispositivo');
            $table->string('uuid')->unique()->comment('Imei o IP del dispositivo');
            $table->foreignId('device_type_id')->comment('Tipo del dispositivo')->constrained('devices_types');
            $table->string('version')->nullable()->default(NULL)->comment('Versión del dispositivo');
            $table->boolean('active')->default('1')->comment('Indica si el dispositivo está activo (0: No está activo, 1: Está activo)');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `devices` comment 'Dispositivos que van a usar la apliación'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
