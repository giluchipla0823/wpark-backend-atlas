<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 75);
            $table->string('surname')->nullable()->default('NULL');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password', 100);
            $table->rememberToken();
            $table->boolean('first_login')->default('0');
            $table->datetime('last_login')->nullable();
            $table->boolean('online')->default('0');
            $table->date('last_change_password')->nullable();
            $table->integer('admin_pin')->nullable()->unsigned();  // ¿?
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
        Schema::dropIfExists('users');
    }
}
