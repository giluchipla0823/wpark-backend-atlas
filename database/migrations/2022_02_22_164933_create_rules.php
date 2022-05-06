<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('countdown')->nullable()->unsigned();
            $table->integer('priority')->nullable()->unsigned();
            $table->foreignId('predefined_zone_id')->nullable()->constrained('rows'); // TODO: Verificar relaciones de esta tabla
            $table->foreignId('overflow_id')->nullable()->constrained('rows');
            $table->foreignId('next_state_id')->nullable()->constrained('states');
            $table->foreignId('carrier_id')->nullable()->constrained('carriers');
            $table->boolean('active')->default('1');
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
        Schema::dropIfExists('rules');
    }
}
