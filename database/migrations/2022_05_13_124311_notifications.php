<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la notificación');
            $table->foreignId('sender_id')->comment('Identificador del usuario que genera la notificación')->constrained('users');
            $table->foreignId('recipient_id')->nullable()->comment('Indentificador del usuario al que va dirigida la notificación')->constrained('users');
            $table->string('type')->comment('Clase sobre la que se ha creado la notificación');
            $table->nullableMorphs('resourceable');
            $table->string('reference_code')->comment('Código de referencia de la notificación');
            $table->text('data')->comment('Datos relevantes sobre la notificación');
            $table->datetime('reat_at')->nullable()->comment('Fecha y hora de cuando se ha leido la notificación');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `notifications` comment 'Notificaciones de avisos en la aplicación'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
