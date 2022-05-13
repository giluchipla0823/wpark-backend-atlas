<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateStages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la estación');
            $table->string('name')->comment('Nombre de la estación');
            $table->string('code', 5)->comment('Código de la estación');
            $table->string('description')->nullable()->comment('Descripción de la estación');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `stages` comment 'Estaciones por las que pasa un vehículo'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stages');
    }
}
