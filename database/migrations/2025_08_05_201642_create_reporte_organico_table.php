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
        $table->string('servicio_organico')->nullable();
        $table->string('nomenclatura_organico')->nullable();
        $table->string('cargo_organico')->nullable();
        $table->string('grado_organico')->nullable();
        $table->string('personal_organico')->nullable();
        $table->integer('numero_organico_ideal')->nullable();
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
