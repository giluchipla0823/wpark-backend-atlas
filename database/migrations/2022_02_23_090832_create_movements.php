<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMovements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id()->comment('Identificador único del movimiento');
            $table->foreignId('vehicle_id')->comment('Indica el vehículo que se mueve')->constrained('vehicles');
            $table->foreignId('user_id')->comment('Indica el usuario que está moviendo el vehículo')->constrained('users');
            $table->string('origin_position_type')->comment('Indica el tipo de posición slot o parking de origen');
            $table->integer('origin_position_id')->unsigned()->comment('Indica la posición desde donde se hace el movimiento');
            $table->string('destination_position_type')->comment('Indica el tipo de posición slot o parking de destino');
            $table->integer('destination_position_id')->unsigned()->comment('Indica la posición haciá donde se hace el movimiento');
            $table->string('category')->nullable()->comment('Nombre de la categoría (regla) que se aplica en ese movimiento');
            $table->boolean('confirmed')->default('0')->comment('Indica si el movimiento se ha confirmado (0: No está confirmado, 1: Está confirmado)');
            $table->boolean('canceled')->default('0')->comment('Indica si el movimiento se ha cancelado (0: No está cancelado, 1: Está cancelado)');
            $table->boolean('manual')->default('0')->comment('Indica si el movimiento es el recomendado o manual (0: Recomendado, 1: Manual)');
            $table->datetime('dt_start')->comment('Fecha y hora del comienzo del movimiento');
            $table->datetime('dt_end')->nullable()->comment('Fecha y hora del final del movimiento');
            $table->text('comments')->nullable()->comment('Comentarios sobre la fila');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `movements` comment 'Movimientos realizados de los vehículos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movements');
    }
}
