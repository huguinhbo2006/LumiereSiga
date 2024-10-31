<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Preinscripcionprocedencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preinscripcionprocedencias', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idTipoEscuela');
            $table->integer('idEscuela');
            $table->integer('idEstado');
            $table->integer('idMunicipio');
            $table->integer('intentos');
            $table->double('promedio');
            $table->boolean('eliminado');
            $table->boolean('activo');
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
        Schema::dropIfExists('preinscripcionprocedencias');
    }
}
