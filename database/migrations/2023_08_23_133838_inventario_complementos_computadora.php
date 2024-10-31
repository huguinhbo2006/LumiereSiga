<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InventarioComplementosComputadora extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventarioobjetos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idTipo');
            $table->string('descripcion');
            $table->integer('estatus');
            $table->integer('computadoras');
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
        Schema::dropIfExists('inventarioobjetos');
    }
}
