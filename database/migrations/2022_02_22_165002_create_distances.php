<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDistances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distances', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la distancia');
            $table->foreignId('origin_slot_id')->comment('Indica la posición (slot) de origen')->constrained('slots');
            $table->foreignId('destination_slot_id')->comment('Indica la posición (slot) de destino')->constrained('slots');
            $table->integer('seconds')->unsigned()->comment('Tiempo en segundos que se tarda de la posición de origen a la de destino');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `distances` comment 'Distancias entre un punto de origen y el destino, se calcula el tiempo en segundos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distances');
    }
}
