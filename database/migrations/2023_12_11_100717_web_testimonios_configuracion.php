<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WebTestimoniosConfiguracion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webtestimoniosconfiguraciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('idConfiguracion');
            $table->longText('imagen');
            $table->boolean('esVideo');
            $table->mediumText('contenido');
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
        Schema::dropIfExists('webtestimoniosconfiguraciones');
    }
}
