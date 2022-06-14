<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('movements', function (Blueprint $table) {
            $table->unsignedBigInteger('device_id')->nullable()->after('user_id')->comment('Indica el dispositivo con el que se hace el movimiento.');
            $table->foreign('device_id')->references('id')->on('devices');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('movements', function (Blueprint $table) {
            $table->dropForeign('device_id');
            $table->dropColumn('device_id');
        });

        Schema::enableForeignKeyConstraints();
    }
}
