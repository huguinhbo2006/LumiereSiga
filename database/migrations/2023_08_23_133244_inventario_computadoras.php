<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InventarioComputadoras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventariocomputadoras', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idSucursal');
            $table->integer('idUsuario');
            $table->integer('idTipo');
            $table->string('procesador');
            $table->string('ram');
            $table->string('disco');
            $table->string('windows');
            $table->string('office');
            $table->integer('estatus');
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
        Schema::dropIfExists('inventariocomputadoras');
    }
}
