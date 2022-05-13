<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRulesConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules_conditions', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la relación');
            $table->foreignId('rule_id')->comment('Identificador de la regla')->constrained('rules');
            $table->foreignId('condition_id')->comment('Identificador de la condición')->constrained('conditions');
            $table->morphs('conditionable');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `rules_conditions` comment 'Relación de las reglas con las condiciones. Los campos conditionable_type y conditionable_id indican la clase de la condición que aplica y su id correspondiente.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rules_conditions');
    }
}
