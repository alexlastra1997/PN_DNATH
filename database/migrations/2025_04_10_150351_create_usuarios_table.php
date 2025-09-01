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
        $table->string('estado_civil')->nullable();
        $table->string('promocion')->nullable();
        $table->string('cdg_promocion')->nullable();
        $table->string('cuadro_policial')->nullable();
        $table->string('fecha_ingreso')->nullable();
        $table->text('domicilio')->nullable();
        $table->string('provincia_trabaja')->nullable();
        $table->string('provincia_vive')->nullable();
        $table->string('pase_ucp_ccp_cpl')->nullable();
        $table->text('capacitacion')->nullable();
        $table->text('titulos')->nullable();
        $table->text('titulos_senescyt')->nullable();
        $table->string('contrato_estudios')->nullable();
        $table->string('conyuge_policia_activo')->nullable();
        $table->string('enf_catast_sp')->nullable();
        $table->string('enf_catast_conyuge_hijos')->nullable();
        $table->string('discapacidad_sp')->nullable();
        $table->string('discapacidad_conyuge_hijos')->nullable();
        $table->string('hijos_menor_igual_18')->nullable(); // equivale a `HIJOS<=18`
        $table->text('alertas')->nullable();
        $table->string('alerta_devengacion')->nullable();
        $table->string('alerta_devengacion_fecha')->nullable();
        $table->string('alerta_devengacion_estado')->nullable();
        $table->string('alerta_marco_legal')->nullable();
        $table->string('alerta_marco_legal_fecha')->nullable();
        $table->string('alerta_marco_legal_estado')->nullable();
        $table->text('meritos')->nullable();
        $table->integer('num_demerito')->nullable();
        $table->string('novedad_situacion')->nullable();
        $table->text('observacion_tenencia')->nullable();
        $table->text('alertas_problemas_salud')->nullable();
        $table->text('FaseMaternidadUDGA')->nullable();
        $table->text('fase_maternidad')->nullable();
        $table->text('fecha_final_maternidad')->nullable();
        $table->text('historico_pases')->nullable();
        $table->text('Traslado_temporal')->nullable();
        $table->text('Traslado_eventual')->nullable();
        $table->text('comisiones')->nullable();
        $table->string('fechaPresentacion')->nullable();
        $table->string('ultimo_pase')->nullable();
        $table->string('funcion_origen')->nullable();
        $table->string('fecha_presentacion_traslado')->nullable();
        $table->integer('numeroDias')->nullable();
        $table->string('nomenclatura')->nullable();
        $table->string('funcion_tras')->nullable();
        $table->string('descripcion')->nullable();
        $table->string('pase_anterior')->nullable();
        $table->string('fecha_pase_anterior')->nullable();
        $table->text('designaciones')->nullable();
        $table->text('maternidad')->nullable();
        $table->text('proyeccion_licencia')->nullable();
        $table->integer('dmq')->nullable();
        $table->integer('dmg')->nullable();
        $table->integer('azuay')->nullable();
        $table->integer('bolivar')->nullable();
        $table->integer('canar')->nullable();
        $table->integer('carchi')->nullable();
        $table->integer('cotopaxi')->nullable();
        $table->integer('chimborazo')->nullable();
        $table->integer('el_oro')->nullable();
        $table->integer('esmeraldas')->nullable();
        $table->integer('guayas')->nullable();
        $table->integer('imbabura')->nullable();
        $table->integer('loja')->nullable();
        $table->integer('los_rios')->nullable();
        $table->integer('manabi')->nullable();
        $table->integer('morona')->nullable();
        $table->integer('napo')->nullable();
        $table->integer('pastaza')->nullable();
        $table->integer('pichincha')->nullable();
        $table->integer('tungurahua')->nullable();
        $table->integer('zamora')->nullable();
        $table->integer('galapagos')->nullable();
        $table->integer('sucumbios')->nullable();
        $table->integer('orellana')->nullable();
        $table->integer('s_domingo')->nullable();
        $table->integer('s_elena')->nullable();
        $table->integer('exterior')->nullable();				
        $table->string('fecha_efectiva')->nullable();
        $table->string('nomenclatura_efectiva')->nullable();
        $table->string('funcion_efectiva')->nullable();
        $table->string('dias_efectivos')->nullable();
        $table->string('estado_efectivo')->nullable();
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
