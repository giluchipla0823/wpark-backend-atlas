<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rows', function (Blueprint $table) {
            $table->id()->comment('Identificador único del país');
            $table->string('row_number', 5)->comment('Número de fila del parking');
            $table->foreignId('parking_id')->comment('Indica el parking al que pertenece la fila')->constrained('parkings');
            $table->foreignId('block_id')->nullable()->comment('Indica el bloque de reglas que está asociado a la fila')->constrained('blocks');
            $table->integer('capacity')->unsigned()->comment('Número de slots que tiene la fila');
            $table->integer('fill')->unsigned()->nullable()->default('0')->comment('Número de slots ocupados en la fila');
            $table->integer('capacitymm')->unsigned()->comment('Capacidad en milímetros de la fila');
            $table->integer('fillmm')->unsigned()->nullable()->default('0')->comment('Capacidad en milímetros ocupados de la fila');
            $table->boolean('full')->nullable()->default('0')->comment('Indica si la fila está llena (0: No está llena, 1: Está llena)');
            $table->string('alt_qr')->nullable()->comment('Código QR de la fila');
            $table->text('comments')->nullable()->comment('Comentarios sobre la fila');
            $table->boolean('active')->default('1')->comment('Indica si la fila está activa (0: No está activa, 1: Está activa)');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `rows` comment 'Filas que tiene un parking'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rows');
    }
}
