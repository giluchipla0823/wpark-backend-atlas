<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDesigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('designs', function (Blueprint $table) {
            $table->id()->comment('Identificador único del modelo');
            $table->string('name')->comment('Nombre del modelo');
            $table->string('short_name')->nullable()->default(NULL)->comment('Nombre corto del modelo');
            $table->string('code')->unique()->comment('Código del modelo');
            $table->foreignId('brand_id')->comment('Indica la marca a la que pertence el modelo')->constrained('brands');
            $table->integer('length')->unsigned()->comment('Longitud del modelo');
            $table->integer('width')->unsigned()->comment('Anchura del modelo');
            $table->integer('height')->unsigned()->comment('Altura del modelo');
            $table->integer('weight')->unsigned()->comment('Peso del modelo');
            $table->string('description')->nullable()->comment('Descripción del modelo');
            $table->boolean('manufacturing')->comment('Indica si el modelo es importado o fabricado en campa (0: Importado, 1: Fabricado en campa)');
            $table->longText('svg')->nullable()->comment('Imagen del modelo');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `designs` comment 'Modelos de los vehículos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('designs');
    }
}
