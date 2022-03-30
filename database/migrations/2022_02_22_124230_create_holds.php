<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            //$table->integer('count')->unsigned();
            $table->integer('priority')->unsigned();
            $table->boolean('active')->default('1');
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
        Schema::dropIfExists('holds');
    }
}
