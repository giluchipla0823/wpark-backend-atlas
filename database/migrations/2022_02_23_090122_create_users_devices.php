<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_devices', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la relación');
            $table->foreignId('user_id')->comment('Identificador del usuario')->constrained('users');
            $table->foreignId('device_id')->comment('Identificador del dispositivo')->constrained('devices');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `users_devices` comment 'Relación de los usuarios con los dispositivos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_devices');
    }
}
