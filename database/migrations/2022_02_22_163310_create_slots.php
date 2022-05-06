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
            $table->integer('slot_number')->unsigned();
            $table->foreignId('row_id')->constrained('rows');
            $table->integer('capacity')->unsigned();
            $table->integer('fill')->unsigned()->nullable()->default('0');
            $table->integer('capacitymm')->unsigned()->nullable();
            $table->integer('fillmm')->unsigned()->nullable()->default('0');
            $table->text('comments')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
