<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDealers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dealers', function (Blueprint $table) {
            $table->id()->comment('Identificador único del distribuidor');
            $table->string('name')->comment('Nombre del distribuidor');
            $table->string('code')->unique()->comment('Código del distribuidor');
            $table->string('zip_code')->comment('Código postal del distribuidor');
            $table->string('city')->comment('Ciudad del distribuidor');
            $table->string('street')->comment('Dirección del distribuidor');
            $table->string('country')->comment('País del distribuidor');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `dealers` comment 'Distribuidores donde serán enviados los vehículos en su salida'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealers');
    }
}
