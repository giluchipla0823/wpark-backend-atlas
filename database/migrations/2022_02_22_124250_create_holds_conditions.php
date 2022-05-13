<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateHoldsConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holds_conditions', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la relación');
            $table->foreignId('hold_id')->comment('Identificador del bloqueo')->constrained('holds');
            $table->foreignId('condition_id')->comment('Identificador de la condición')->constrained('conditions');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `holds_conditions` comment 'Relación de los bloqueos con las condiciones'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('holds_conditions');
    }
}
