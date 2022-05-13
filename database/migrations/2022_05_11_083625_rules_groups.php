<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RulesGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules_groups', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la relación');
            $table->foreignId('parent_rule_id')->comment('Identificador de la regla agrupada')->constrained('rules');
            $table->foreignId('child_rule_id')->comment('Identificador de la regla simple')->constrained('rules');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `rules_groups` comment 'Relación de las reglas agrupadas con las reglas simples'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rules_groups');
    }
}
