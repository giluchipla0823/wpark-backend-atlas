<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('Identificador único del usuario');
            $table->string('name', 75)->comment('Nombre del usuario');
            $table->string('surname')->nullable()->default('NULL')->comment('Apellidos del usuario');
            $table->string('email')->unique()->comment('Email del usuario');
            $table->string('username')->unique()->comment('Nombre de usuario');
            $table->string('password', 100)->comment('Contraseña del usuario');
            $table->rememberToken()->comment('Token generado tras usar el servicio de recordar contraseña');
            $table->boolean('first_login')->default('0')->comment('Indica si el usuario ha entrado ya en la aplicación (0: No ha entrado, 1: Ya ha entrado)');
            $table->datetime('last_login')->nullable()->comment('Fecha y hora de la última vez que el usuario ha entrado en la aplicación');
            $table->boolean('online')->default('0')->comment('Indica si el usuario está conectado (0: No conectado, 1: Conectado)');
            $table->date('last_change_password')->nullable()->comment('Fecha del último cambio de contraseña');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `users` comment 'Usuarios de la aplicación'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
