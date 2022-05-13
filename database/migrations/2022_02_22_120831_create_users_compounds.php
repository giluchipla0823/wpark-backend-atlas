<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersCompounds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_compounds', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la relación');
            $table->foreignId('user_id')->comment('Identificador del usuario')->constrained('users');
            $table->foreignId('compound_id')->comment('Identificador de la campa')->constrained('compounds');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `users_compounds` comment 'Relación de los usuarios con las campas'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_compounds');
    }
}
