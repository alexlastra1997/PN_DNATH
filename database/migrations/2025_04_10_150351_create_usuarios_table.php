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
