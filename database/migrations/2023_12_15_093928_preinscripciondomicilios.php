<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Preinscripciondomicilios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preinscripciondomicilios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('calle');
            $table->string('numeroExterior');
            $table->string('numeroInterior');
            $table->string('colonia');
            $table->string('codigoPostal');
            $table->integer('idEstado');
            $table->integer('idMunicipio');
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
        Schema::dropIfExists('preinscripciondomicilios');
    }
}
