<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParkings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parkings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('area_id')->constrained('areas');
            $table->foreignId('parking_type_id')->constrained('parking_types');
            $table->integer('start_row')->nullable()->unsigned();
            $table->integer('end_row')->nullable()->unsigned();
            $table->integer('capacity')->nullable()->unsigned();
            $table->integer('fill')->nullable()->unsigned();
            $table->boolean('full')->nullable()->default('0');
            $table->boolean('order')->nullable()->default('0');
            $table->boolean('active')->default('1');
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('parkings');
    }
}
