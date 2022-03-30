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
            //$table->foreignId('compound_id')->constrained('compounds');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('origin_slot_id')->constrained('slots');
            $table->foreignId('destination_slot_id')->constrained('slots');
            $table->foreignId('rule_id')->constrained('rules'); // Confirmar que la tabla de movimientos tiene que estar asociada a una regla o a un bloque
            $table->datetime('dt_start');
            $table->datetime('dt_end');
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
