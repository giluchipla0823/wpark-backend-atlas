<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateZones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la zona');
            $table->string('name')->comment('Nombre de la zona');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `zones` comment 'Zonas que tiene una determinada campa'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zones');
    }
}
