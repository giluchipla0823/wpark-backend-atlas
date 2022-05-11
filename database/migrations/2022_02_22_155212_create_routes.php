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
            $table->string('cdm_code', 5)->unique();
            $table->foreignId('route_type_id')->constrained('route_types');
            $table->foreignId('carrier_id')->constrained('carriers');
            $table->foreignId('destination_code_id')->constrained('destination_codes');
            $table->foreignId('origin_compound_id')->constrained('compounds');
            $table->foreignId('destination_compound_id')->nullable()->constrained('compounds');
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
