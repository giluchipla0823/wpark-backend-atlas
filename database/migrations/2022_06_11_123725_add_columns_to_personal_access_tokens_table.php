<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPersonalAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('compound_id')->after('token')->comment('Indica la campa seleccionada en el inicio de sesión.');
            $table->foreign('compound_id')->references('id')->on('compounds');

            $table->unsignedBigInteger('device_id')->nullable()->after('token')->comment('Indica el dispositivo seleccionado en el inicio de sesión.');
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

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropForeign('compound_id');
            $table->dropColumn('compound_id');
            $table->dropForeign('device_id');
            $table->dropColumn('device_id');
        });

        Schema::enableForeignKeyConstraints();
    }
}
