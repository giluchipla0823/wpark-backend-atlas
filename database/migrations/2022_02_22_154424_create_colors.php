<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateColors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colors', function (Blueprint $table) {
            $table->id()->comment('Identificador único del color');
            $table->string('name')->comment('Nombre específico del color');
            $table->string('code')->unique()->comment('Código del color');
            $table->string('simple_name')->comment('Nombre básico del color');
            $table->string('hex')->nullable()->unique()->comment('Código hexadecimal del color');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `colors` comment 'Colores de los vehículos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('colors');
    }
}
