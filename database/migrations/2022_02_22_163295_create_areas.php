<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('compound_id')->constrained('compounds');
            $table->foreignId('zone_id')->constrained('zones'); // nullable o notNullable ¿?
            $table->integer('rows')->unsigned();
            //$table->integer('columns')->unsigned()->default('8');
            $table->integer('capacity')->unsigned();
            //$table->string('latitude', 20)->nullable();
            //$table->string('longitude', 20)->nullable();
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
        Schema::dropIfExists('areas');
    }
}
