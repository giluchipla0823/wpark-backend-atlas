<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDestinationCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('destination_codes', function (Blueprint $table) {
            $table->id()->comment('Identificador único del código de destino');
            $table->string('name', 100)->comment('Nombre del código de destino');
            $table->string('code', 5)->unique()->comment('Código del código de destino');
            $table->foreignId('country_id')->comment('Indica el país del código de destino')->constrained('countries');
            $table->string('description')->nullable()->default(NULL)->comment('Descripción del código de destino');
            $table->boolean('active')->default('1')->comment('Indica si el código de destino está activo (0: No está activo, 1: Está activo)');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `destination_codes` comment 'Códigos de destino que hacen referencia a donde van a ir los vehículos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('destination_codes');
    }
}
