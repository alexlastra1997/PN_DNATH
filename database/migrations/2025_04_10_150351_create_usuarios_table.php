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
    Schema::create('usuarios', function (Blueprint $table) {
        $table->id();
        $table->string('cedula')->nullable();
        $table->string('grado')->nullable();
        $table->string('apellidos_nombres')->nullable();
        $table->string('sexo')->nullable();
        $table->string('tipo_personal')->nullable();
        $table->string('antiguedad')->nullable();
        $table->string('unidad')->nullable();
        $table->string('funcion')->nullable();
        $table->string('causa_pase')->nullable();
        $table->string('estado_civil')->nullable();
        $table->string('promocion')->nullable();
        $table->string('cdg_promocion')->nullable();
        $table->string('fecha_ingreso')->nullable();
        $table->string('unidad_anterior')->nullable();
        $table->string('fecha_pase_anterior')->nullable();
        $table->string('tiempo_unidad_anterior')->nullable();
        $table->string('fecha_pase_actual')->nullable();
        $table->string('tiempo_ultimo_pase')->nullable();
        $table->string('fecha_actual')->nullable();
        $table->string('tiempo_pase_formula')->nullable();
        $table->string('fecha_presentacion')->nullable();
        $table->string('servicio_grupal')->nullable();
        $table->text('domicilio')->nullable();
        $table->string('provincia_trabaja')->nullable();
        $table->string('provincia_vive')->nullable();
        $table->string('pase_ucp_ccp_cpl')->nullable();
        $table->string('cuadro_policial')->nullable();
        $table->text('capacitacion')->nullable();
        $table->text('titulos')->nullable();
        $table->text('titulos_senescyt')->nullable();
        $table->string('contrato_estudios')->nullable();
        $table->string('conyuge_policia_activo')->nullable();
        $table->string('enf_catast_sp')->nullable();
        $table->string('enf_catast_conyuge_hijos')->nullable();
        $table->string('enf_catast_padres')->nullable();
        $table->string('discapacidad_sp')->nullable();
        $table->string('discapacidad_conyuge_hijos')->nullable();
        $table->string('discapacidad_padres')->nullable();
        $table->string('hijos18')->nullable();
        $table->string('registra_observacion_tenencia', 5000)->nullable();
        $table->text('alertas')->nullable();
        $table->text('meritos')->nullable();
        $table->integer('num_demerito')->nullable();
        $table->string('novedad_situacion')->nullable();
        $table->text('historico_pases')->nullable();
        $table->text('designaciones')->nullable();
        $table->text('maternidad')->nullable();
        $table->text('proyeccion_licencia')->nullable();
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
        Schema::dropIfExists('usuarios');
    }
};
