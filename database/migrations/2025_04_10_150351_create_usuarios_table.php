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
            $table->string('apenom', 150);
            $table->string('documento', 30);
            $table->string('idDgpTipoSituacion', 50)->nullable();
            $table->unsignedInteger('idDgpGrado')->nullable();
            $table->date('fechaNacimiento')->nullable();
            $table->char('sexo', 1)->nullable();
            $table->string('idDgpTipoPersonal', 50)->nullable();
            $table->string('grado', 50)->nullable();
            $table->string('siglas', 10)->nullable();
            $table->string('unidad', 100)->nullable();
            $table->string('asignacion', 100)->nullable();
            $table->string('idDgpAsignacion', 50)->nullable();
            $table->string('idDgpFuncionAsignada', 50)->nullable();
            $table->string('funcion', 100)->nullable();
            $table->string('funcionAsignada', 100)->nullable();
            $table->date('fechaAsignacion')->nullable();
            $table->string('idDgpPromocion', 50)->nullable();
            $table->string('antiguedad', 50)->nullable();
            $table->string('idDgpUnidad', 50)->nullable();
            $table->string('idDgpFuncion', 50)->nullable();
            $table->string('idDgpUnidadAs', 50)->nullable();
            $table->string('idDgpFuncionAs', 50)->nullable();
            $table->date('fechaIngreso')->nullable();
            $table->date('fechaAscenso')->nullable();
            $table->date('fechaPaseDesde')->nullable();
            $table->string('cuadro', 50)->nullable();
            $table->string('servicioPolicial', 100)->nullable();
            $table->string('idDgpServicioPolicial', 50)->nullable();
            $table->string('servicioGrupal', 100)->nullable();
            $table->string('idDgpServicioGrupal', 50)->nullable();
            $table->string('tipoSangre', 10)->nullable();
            $table->string('provinciaVive', 50)->nullable();
            $table->string('provinciaTrabaja', 50)->nullable();
            $table->date('fechaPresentacion')->nullable();
            $table->string('descEstaCivil', 30)->nullable();
            $table->string('unidadOrigen', 100)->nullable();
            $table->string('idUnidadOrigen', 50)->nullable();
            $table->string('causaPase', 100)->nullable();
            $table->string('nombreComun', 255)->nullable();
            $table->string('codSenpladesUATrabaja', 20)->nullable();
            $table->string('codProvTrabaja', 10)->nullable();
            $table->string('DistribucionSENPLADES', 50)->nullable();
            $table->string('Zona', 20)->nullable();
            $table->string('Subzona', 255)->nullable();
            $table->char('esdistrito', 1)->nullable();
            $table->string('Distrito', 50)->nullable();
            $table->string('Circuito', 50)->nullable();
            $table->string('Subcircuito', 50)->nullable();
            $table->string('lugarNacimiento', 100)->nullable();
            $table->decimal('estatura', 4, 2)->nullable();
            $table->char('fallecido', 1)->nullable();
            $table->string('novedad', 100)->nullable();
            $table->date('fechaNovedadDesde')->nullable();
            $table->date('fechaNovedadHasta')->nullable();
            $table->string('documentoNovedad', 50)->nullable();
            $table->string('especializado', 50)->nullable();
            $table->string('regionGeografica', 50)->nullable();
            $table->string('codigoSenpladesAsignacion', 20)->nullable();
            $table->string('proyeccionLicencia', 50)->nullable();
            $table->string('totalMeritos', 10)->nullable();
            $table->integer('totalHArresto')->nullable();
            $table->string('represionSimple', 50)->nullable();
            $table->string('represionFormal', 50)->nullable();
            $table->string('represionSevera', 50)->nullable();
            $table->string('conyuge', 100)->nullable();
            $table->string('Padre', 100)->nullable();
            $table->string('totalHijos', 5)->nullable();
            $table->string('hijosM18', 5)->nullable();
            $table->string('hijos1218', 5)->nullable();
            $table->string('hijosm12', 5)->nullable();
            $table->string('Madre', 100)->nullable();
            $table->string('zonaTrabaja', 50)->nullable();
            $table->char('salirPase', 1)->nullable();
            $table->string('zonaVive', 50)->nullable();
            $table->string('idProvinciaVive', 10)->nullable();
            $table->string('TiempoPase', 50)->nullable();
            $table->string('discapacidad', 50)->nullable();
            $table->string('discapacidadDependiente', 50)->nullable();
            $table->string('opcionPase1', 50)->nullable();
            $table->string('opcionPase2', 50)->nullable();
            $table->string('opcionPase3', 50)->nullable();
            $table->string('cedulaCygPolicial', 30)->nullable();
            $table->string('periodoMaternidad', 50)->nullable();
            $table->string('especializacion', 100)->nullable();
            $table->string('contratoEstudios', 50)->nullable();
            $table->string('idGenPersona', 50)->nullable();
            $table->string('Eje', 50)->nullable();
            $table->string('Promocion', 50)->nullable();
            $table->string('TiempoUnidadActual', 50)->nullable();
            $table->string('TiempoUnidadAnterior', 50)->nullable();
            
            // Textos largos optimizados
            $table->text('Capacitacion')->nullable();
            $table->text('EnfCatastSP')->nullable();
            $table->text('EnfCatastFamiliares')->nullable();
            $table->text('Alertas')->nullable();
            $table->text('ObservacionFamiliar')->nullable();
            $table->text('NovedadSituacion')->nullable();
            $table->text('Historico_Pases')->nullable();
            $table->text('TrasladosTemporales')->nullable();
        $table->string('cedula')->nullable();
        $table->string('grado')->nullable();
        $table->string('apellidos_nombres')->nullable();
        $table->string('sexo')->nullable();
        $table->string('tipo_personal')->nullable();
        $table->string('antiguedad')->nullable();
        $table->string('unidad')->nullable();
        $table->string('funcion')->nullable();
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
        $table->string('discapacidad_sp')->nullable();
        $table->string('discapacidad_conyuge_hijos')->nullable();
        $table->string('hijos_menor_igual_18')->nullable(); // equivale a `HIJOS<=18`
        $table->text('alertas')->nullable();
        $table->text('meritos')->nullable();
        $table->integer('num_demerito')->nullable();
        $table->string('novedad_situacion')->nullable();
        $table->text('historico_pases')->nullable();
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
