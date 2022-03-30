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
            $table->string('wprk', 50);
            $table->string('oprk', 25)->nullable()->default('NULL');
            $table->string('code', 50)->unique();
            $table->foreignId('carrier_id')->nullable()->constrained('carriers');
            $table->foreignId('rule_id')->constrained('rules');
            $table->boolean('ready')->default('0');
            $table->foreignId('compound_id')->nullable()->constrained('compounds');
            $table->boolean('processed')->default('0');
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
        Schema::dropIfExists('loads');
    }
}
