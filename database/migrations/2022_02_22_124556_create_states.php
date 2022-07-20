<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateStates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('states', function (Blueprint $table) {
            $table->id()->comment('Identificador único del estado');
            $table->string('name')->comment('Nombre del estado');
            $table->string('description')->nullable()->default(NULL)->comment('Descripción del estado');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `states` comment 'Estados de los vehículos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('states');
    }
}
