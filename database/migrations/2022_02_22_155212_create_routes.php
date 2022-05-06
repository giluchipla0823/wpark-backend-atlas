<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 5)->unique();
            $table->foreignId('carrier_id')->constrained('carriers');
            $table->foreignId('exit_transport_id')->nullable()->contrained('transports');
            $table->foreignId('origin_compound_id')->constrained('compounds');
            $table->foreignId('destination_compound_id')->constrained('compounds');
            $table->foreignId('dealer_id')->nullable()->constrained('dealers');
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
        Schema::dropIfExists('routes');
    }
}
