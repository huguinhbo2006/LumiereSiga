<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FormularioCuponDescuento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formulariocupondescuentos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->string('celular');
            $table->string('descuento');
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
        Schema::dropIfExists('formulariocupondescuentos');
    }
}
