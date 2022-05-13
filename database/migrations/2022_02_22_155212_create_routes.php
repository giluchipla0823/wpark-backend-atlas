<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la ruta');
            $table->string('name', 100)->comment('Nombre de la ruta');
            $table->string('cdm_code', 5)->unique()->comment('Código de la ruta');
            $table->foreignId('route_type_id')->comment('Indica el tipo de ruta')->constrained('route_types');
            $table->foreignId('carrier_id')->comment('Indica la empresa de transporte que hace la ruta')->constrained('carriers');
            $table->foreignId('destination_code_id')->comment('Indica el código de destino de la ruta')->constrained('destination_codes');
            $table->foreignId('origin_compound_id')->comment('Indica la campa de origen')->constrained('compounds');
            $table->foreignId('destination_compound_id')->nullable()->comment('Indica la campa de destino')->constrained('compounds');
            $table->text('comments')->nullable()->comment('Comentarios sobre la ruta');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `routes` comment 'Rutas que puede seguir un vehículo para llegar a su destino'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('routes');
    }
}
