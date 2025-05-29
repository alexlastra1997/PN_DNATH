<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('reporte_organico', function (Blueprint $table) {
        $table->id();
        $table->string('unidad')->nullable();
        $table->string('nomenclatura')->nullable();
        $table->string('grado')->nullable();
        $table->string('funcion')->nullable();
        $table->string('tipo_personal')->nullable();
        $table->integer('cantidad_ideal')->nullable();
        $table->integer('cantidad_real')->nullable();
        $table->integer('diferencia')->nullable();
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
        Schema::dropIfExists('reporte_organico');
    }
};
