<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            //$table->foreignId('rule_id')->constrained('rules');
            $table->foreignId('condition_id')->constrained('conditions');
            $table->morphs('conditionable');
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
        Schema::dropIfExists('rules_conditions');
    }
}
