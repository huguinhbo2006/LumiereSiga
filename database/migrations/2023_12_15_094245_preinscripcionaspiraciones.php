<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Preinscripcionaspiraciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preinscripcionaspiraciones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idUniversidad');
            $table->integer('idCentroUniversitario');
            $table->integer('idCarrera');
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
        Schema::dropIfExists('preinscripcionaspiraciones');
    }
}
