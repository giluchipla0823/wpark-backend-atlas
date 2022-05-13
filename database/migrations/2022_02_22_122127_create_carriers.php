<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCarriers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carriers', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la empresa de transporte');
            $table->string('name')->comment('Nombre de la empresa de transporte');
            $table->string('short_name')->comment('Nombre corto de la empresa de transporte');
            $table->string('code', 25)->unique()->comment('Código de la empresa de transporte');
            $table->boolean('active')->default('1')->comment('Indica si el transportista está activo (0: No está activo, 1: Está activo)');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `carriers` comment 'Empresas de transporte'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carriers');
    }
}
