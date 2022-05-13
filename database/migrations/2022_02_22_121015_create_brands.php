<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateBrands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la marca');
            $table->string('name')->comment('Nombre de la marca');
            $table->string('code')->unique()->comment('Código de la marca');
            $table->foreignId('compound_id')->nullable()->comment('Identificador de la campa')->constrained('compounds');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `brands` comment 'Marcas de los vehículos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brands');
    }
}
