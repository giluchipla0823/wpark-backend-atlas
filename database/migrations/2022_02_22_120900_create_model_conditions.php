<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateModelConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Se asocia a las conciciones para indicar si será una condición de holds o de rules
        Schema::create('model_conditions', function (Blueprint $table) {
            $table->id()->comment('Identificador único del tipo de la condición');
            $table->string('name')->comment('Nombre del tipo de la condición, puede ser HOLD o RULE');
            $table->string('model')->comment('Modelo del tipo de la condición');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `model_conditions` comment 'Tipos para las condiciones. Pueden ser condiciones para reglas o para bloqueos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_conditions');
    }
}
