<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la regla');
            $table->string('name')->comment('Nombre de la regla');
            $table->integer('countdown')->nullable()->unsigned()->comment('Número de vehículos máximo para aplicarles la regla');
            $table->integer('priority')->nullable()->unsigned()->comment('Indica el orden de prioridad de la regla');
            $table->boolean('is_group')->default('0')->comment('Indica si es una regla simple o un grupo de reglas (0: Regla simple, 1: Grupo de reglas)');
            $table->boolean('final_position')->default('1')->comment('Indica si la regla es de posición final (0: No es posición final, 1: Es posición final)');
            $table->foreignId('predefined_zone_id')->nullable()->comment('Indica el parking al que va asociada la regla')->constrained('parkings');
            $table->foreignId('carrier_id')->nullable()->comment('Indica el transportista por defecto asociado a la regla')->constrained('carriers');
            $table->boolean('active')->default('1')->comment('Indica si la regla está activa (0: No está activa, 1: Está activa)');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `rules` comment 'Reglas que se aplican a los coches según sus características y a bloques para identificar su posición en la campa'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rules');
    }
}
