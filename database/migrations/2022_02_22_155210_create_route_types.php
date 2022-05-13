<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRouteTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_types', function (Blueprint $table) {
            $table->id()->comment('Identificador único del tipo de ruta');
            $table->string('name')->comment('Nombre del tipo de ruta');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `route_types` comment 'Tipos de ruta. Pueden ser de tipo alternativa o por defecto'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_types');
    }
}
