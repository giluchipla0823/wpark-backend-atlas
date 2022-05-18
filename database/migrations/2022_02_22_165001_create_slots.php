<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSlots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id()->comment('Identificador único del slot');
            $table->integer('slot_number')->unsigned()->comment('Número de slot de la fila');
            $table->foreignId('row_id')->comment('Indica la fila a la que pertenece el slot')->constrained('rows');
            $table->integer('capacity')->unsigned()->comment('Capacidad del slot');
            $table->integer('fill')->unsigned()->nullable()->default('0')->comment('Indica si el slot está ocupado');
            $table->integer('capacitymm')->unsigned()->nullable()->comment('Capacidad en milímetros del slot');
            $table->integer('fillmm')->unsigned()->nullable()->default('0')->comment('Capacidad en milímetros ocupados del slot');
            $table->text('comments')->nullable()->comment('Comentarios sobre el slot');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `slots` comment 'Slots que indican la posición exacta del vehículo dentro de una fila'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slots');
    }
}
