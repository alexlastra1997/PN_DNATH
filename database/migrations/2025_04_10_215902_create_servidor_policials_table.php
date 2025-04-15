<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServidorPolicialsTable extends Migration
{
    public function up()
    {
        Schema::create('servidor_policials', function (Blueprint $table) {
            $table->id();
            $table->string('cedula');
            $table->string('apellidos_nombres');
            $table->integer('hijos18')->nullable();  // Cambia el tipo de dato según tu estructura
            // Agrega más campos según sea necesario
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('servidor_policials');
    }
}