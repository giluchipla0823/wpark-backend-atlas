<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCompounds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compounds', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la campa');
            $table->string('name')->comment('Nombre de la campa');
            $table->string('zip_code')->nullable()->comment('Código postal de la campa');
            $table->string('city')->nullable()->comment('Ciudad de la campa');
            $table->string('street')->nullable()->comment('Dirección de la campa');
            $table->string('country')->nullable()->comment('País de la campa');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `compounds` comment 'Campas'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compounds');
    }
}
