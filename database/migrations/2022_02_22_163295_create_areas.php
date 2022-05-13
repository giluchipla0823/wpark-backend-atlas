<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAreas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id()->comment('Identificador único del área');
            $table->string('name')->comment('Nombre del área');
            $table->foreignId('compound_id')->comment('Indica la campa a la que pertence el área')->constrained('compounds');
            $table->foreignId('zone_id')->comment('Indica la zona a la que pertence el área')->constrained('zones');
            $table->integer('rows')->nullable()->unsigned()->comment('Número de filas que tiene el área');
            $table->integer('capacity')->nullable()->unsigned()->comment('Capacidad (total de slots) del área');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `areas` comment 'Áreas que tiene cada zona de la campa'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('areas');
    }
}
