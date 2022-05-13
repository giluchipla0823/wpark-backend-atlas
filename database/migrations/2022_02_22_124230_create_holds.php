<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateHolds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holds', function (Blueprint $table) {
            $table->id()->comment('Identificador único del bloqueo');
            $table->string('name')->comment('Nombre del bloqueo');
            $table->string('code')->unique()->comment('Código del bloqueo');
            $table->integer('priority')->unsigned()->comment('Prioridad del bloqueo');
            $table->foreignId('role_id')->nullable()->comment('Indica el rol del usuario que puede aplicar un bloqueo')->constrained('roles');
            $table->boolean('active')->default('1')->comment('Indica si el bloqueo está activo (0: No está activo, 1: Está activo)');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `holds` comment 'Bloqueos o retenciones que se le pueden aplicar a los vehículos'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('holds');
    }
}
