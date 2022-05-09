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
            $table->string('lvin', 17)->unique();
            $table->string('vin_short', 7);
            $table->foreignId('design_id')->constrained('designs');
            $table->foreignId('color_id')->constrained('colors');
            $table->foreignId('destination_code_id')->constrained('destination_codes');
            $table->foreignId('entry_transport_id')->constrained('transports');
            $table->foreignId('load_id')->nullable()->constrained('loads');
            $table->foreignId('dealer_id')->nullable()->constrained('dealers');
            $table->string('eoc')->unique();
            $table->foreignId('last_rule_id')->nullable()->constrained('rules');
            $table->foreignId('shipping_rule_id')->nullable()->constrained('rules');
            $table->string('info', 100)->nullable()->default('NULL');
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
        Schema::dropIfExists('vehicles');
    }
}
