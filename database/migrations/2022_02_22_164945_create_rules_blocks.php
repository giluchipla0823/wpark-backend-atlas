<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRulesBlocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules_blocks', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la relación');
            $table->foreignId('rule_id')->comment('Identificador de la regla')->constrained('rules');
            $table->foreignId('block_id')->comment('Identificador del bloque')->constrained('blocks');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `rules_blocks` comment 'Relación de las reglas con los bloques'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rules_blocks');
    }
}
