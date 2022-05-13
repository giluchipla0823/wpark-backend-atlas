<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateParkings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parkings', function (Blueprint $table) {
            $table->id()->comment('Identificador único del parking');
            $table->string('name')->comment('Nombre del parking');
            $table->foreignId('area_id')->comment('Indica el área al que pertenece el parking')->constrained('areas');
            $table->foreignId('parking_type_id')->comment('Indica el tipo de parking')->constrained('parking_types');
            $table->integer('start_row')->nullable()->unsigned()->comment('La fila del área en la que empieza el parking');
            $table->integer('end_row')->nullable()->unsigned()->comment('La fila del área en la que termina el parking');
            $table->integer('capacity')->nullable()->unsigned()->comment('Capacidad (número de slots) del parking');
            $table->integer('fill')->nullable()->unsigned()->comment('Número de slots ocupados en el parking');
            $table->boolean('full')->nullable()->default('0')->comment('Indica si el parking está lleno (0: No está lleno, 1: Está lleno)');
            $table->boolean('order')->nullable()->default('0')->comment('Indica si se comienza a llenar desde la primera fila o la última (0: Orden Descendente, 1: Orden Ascendente)');
            $table->boolean('active')->default('1')->comment('Indica si el parking está activo (0: No está activo, 1: Está activo)');
            $table->text('comments')->nullable()->comment('Comentarios sobre el parking');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `parkings` comment 'Parkings que pertenecen a un área y pueden contener filas si son de tipo FILA o ESPIGA'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parkings');
    }
}
