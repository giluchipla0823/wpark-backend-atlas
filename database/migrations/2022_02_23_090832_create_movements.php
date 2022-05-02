<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('origin_position_id')->unsigned();
            $table->string('origin_position_type');
            $table->integer('destination_position_id')->unsigned();
            $table->string('destination_position_type');
            $table->foreignId('rule_id')->nullable()->constrained('rules'); // Confirmar que la tabla de movimientos tiene que estar asociada a una regla o a un bloque
            /* El campo ready o pendiente no sería necesario ya que si se ha creado
            el movimiento y los campos confirmed y canceled ambos son 0 quiere decir
            que el movimiento aún está en proceso.
            */
            //$table->boolean('ready')->default('1');
            $table->boolean('confirmed')->default('0');
            $table->boolean('canceled')->default('0');
            $table->datetime('dt_start');
            $table->datetime('dt_end')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
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
