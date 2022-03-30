<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDestinationCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('destination_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 5)->unique();
            $table->foreignId('route_id')->nullable()->constrained('routes');
            $table->foreignId('country_id')->constrained('countries');
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
        Schema::dropIfExists('destination_codes');
    }
}
