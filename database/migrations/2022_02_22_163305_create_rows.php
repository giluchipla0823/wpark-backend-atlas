<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->integer('row_number')->unsigned();
            $table->foreignId('parking_id')->constrained('parkings');
            $table->foreignId('block_id')->nullable()->constrained('blocks');
            $table->integer('capacity')->unsigned();
            $table->integer('fill')->unsigned()->nullable()->default('0');
            $table->integer('capacitymm')->unsigned();
            $table->integer('fillmm')->unsigned()->nullable()->default('0');
            $table->string('alt_qr');
            $table->text('comments')->nullable();
            $table->tinyInteger('active')->unsigned();
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
        Schema::dropIfExists('rows');
    }
}
