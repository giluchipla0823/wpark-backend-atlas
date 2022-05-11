<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loads', function (Blueprint $table) {
            $table->id();
            $table->string('transport_identifier', 50)->unique();
            $table->string('license_plate', 50);
            $table->string('trailer_license_plate', 25)->nullable()->default('NULL');
            $table->foreignId('carrier_id')->nullable()->constrained('carriers');
            $table->foreignId('exit_transport_id')->nullable()->constrained('transports');
            $table->foreignId('compound_id')->nullable()->constrained('compounds');
            $table->boolean('ready')->default('0');
            $table->boolean('processed')->default('0');
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
        Schema::dropIfExists('loads');
    }
}
