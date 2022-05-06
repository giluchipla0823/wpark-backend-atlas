<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('origin_position_id')->unsigned();
            $table->string('origin_position_type');
            $table->integer('destination_position_id')->unsigned();
            $table->string('destination_position_type');
            $table->string('category');
            $table->boolean('confirmed')->default('0');
            $table->boolean('canceled')->default('0');
            $table->boolean('manual')->default('0');
            $table->datetime('dt_start');
            $table->datetime('dt_end')->nullable();
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
        Schema::dropIfExists('movements');
    }
}
