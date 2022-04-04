<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vin', 17)->unique();
            $table->string('vin_short', 7);
            $table->foreignId('design_id')->constrained('designs');
            $table->foreignId('color_id')->constrained('colors');
            $table->foreignId('country_id')->constrained('countries');
            $table->foreignId('destination_code_id')->constrained('destination_codes');
            $table->foreignId('load_id')->nullable()->constrained('loads');
            $table->foreignId('slot_id')->nullable()->constrained('slots');
            $table->foreignId('last_slot_id')->nullable()->constrained('slots');
            $table->foreignId('compound_id')->nullable()->constrained('compounds');
            $table->string('eoc')->unique();
            $table->foreignId('last_rule_id')->nullable()->constrained('rules');
            $table->foreignId('shipping_rule_id')->nullable()->constrained('rules');
            $table->string('info', 100)->nullable()->default('NULL');
            //$table->datetime('dt_onterminal')->nullable();
            //$table->datetime('dt_left')->nullable();
            //$table->boolean('on_route')->default('0');
            $table->boolean('hybrid')->default('0');
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
        Schema::dropIfExists('vehicles');
    }
}
