<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conditions', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la condición');
            $table->string('name')->comment('Nombre de la condición');
            $table->string('description')->nullable()->default('NULL')->comment('Descripción de la condición');
            $table->string('model')->nullable()->comment('Modelo de la condición');
            $table->foreignId('model_condition_id')->comment('Indica si la condición será para un hold o para una regla')->constrained('model_conditions');
            $table->boolean('required')->default('0')->comment('Indica si la condición es obligatoria (0: No es obligatoria, 1: Es obligatoria)');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `conditions` comment 'Condiciones que se podrán seleccionar y añadir a una regla o bloqueo. Puede ser VIN, ESTACIÓN, MODELO, CÓDIGO DE DESTINO y COLOR en el caso de reglas'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conditions');
    }
}
