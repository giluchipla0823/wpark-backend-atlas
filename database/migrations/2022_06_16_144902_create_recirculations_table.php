<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecirculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recirculations', function (Blueprint $table) {
            $table->id()->comment('Identificador único de recirculación');
            $table->foreignId('vehicle_id')->comment('Indica el vehículo que aplicó la recirculación')->constrained('vehicles');
            $table->foreignId('user_id')->comment('Indica el usuario que aplicó la recirculación')->constrained('users');
            $table->string('origin_position_type')->comment('Indica el tipo de posición(slot o parking) de origen del vehículo cuando se hizo la recirculación.');
            $table->integer('origin_position_id')->unsigned()->comment('Indica el id de posición de origen del vehículo cuando se hizo la recirculación.');
            $table->string("message", 100)->comment("Indica el valor de la propiedad responseSt7BoardText1 que devuelve la api de recirculaciones.");
            $table->boolean('success')->comment('Indica si la recirculación fue exitosa o no.');
            $table->boolean('back')->default(0)->comment('Indica si el conductor siguió con el movimiento normal (avanzando sobre la pantalla de los botones OK/NOK/Ventas/ZK) “0” o volvió atrás “1”.');
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
        Schema::dropIfExists('recirculations');
    }
}
