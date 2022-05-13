<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCountries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id()->comment('Identificador único del país');
            $table->string('name')->comment('Nombre del país');
            $table->string('code')->unique()->comment('Código del país');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `countries` comment 'Paises'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
