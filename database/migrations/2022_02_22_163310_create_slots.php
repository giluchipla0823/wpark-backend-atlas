<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->integer('slot_number');
            $table->foreignId('row_id')->constrained('rows');
            $table->integer('capacity')->unsigned();
            $table->integer('fill')->unsigned()->nullable()->default('0');
            $table->integer('capacitymm')->unsigned();
            $table->integer('fillmm')->unsigned()->nullable()->default('0');
            $table->text('comments')->nullable();
            //$table->string('latitude', 20)->nullable();
            //$table->string('longitude', 20)->nullable();
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
        Schema::dropIfExists('slots');
    }
}
