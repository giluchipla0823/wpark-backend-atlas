<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateBlocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id()->comment('Identificador único del bloque');
            $table->string('name')->comment('Nombre del bloque');
            $table->boolean('is_presorting')->default(0)->comment('Indica si el bloque es de presorting (0: No es presorting, 1: Es presorting)');
            $table->boolean('presorting_default')->nullable()->comment('Indica si el bloque de presorting es por defecto (0: No es por defecto, 1: Es por defecto)');
            $table->boolean('active')->default(1)->comment('Indica si el bloque está activo (0: No está activo, 1: Está activo)');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `blocks` comment 'Bloques que irán asignados a las filas y contendrán reglas (rules_blocks)'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blocks');
    }
}
