<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('direccion_unidad_zona_policia')->nullable();
            $table->string('sub_zona_policia')->nullable();
            $table->string('distrito')->nullable();
            $table->string('circuito_departamento_seccion')->nullable();
            $table->string('subcircuito')->nullable();
            $table->string('funcion_asignada')->nullable();
            $table->date('fecha_presentacion_nueva')->nullable();
            $table->string('novedad')->nullable();
            $table->string('dependencia_destino')->nullable();
            $table->string('detalle_novedad_nueva_unidad')->nullable();
            $table->date('fecha_novedad')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('documento_referencia')->nullable();
            $table->string('numero_grupo_trabajo')->nullable();
            $table->string('grupo')->nullable();
            $table->string('modalidad')->nullable();
            $table->string('tipo_sangre')->nullable();
            $table->string('licencia_conducir')->nullable();
            $table->string('numero_celular')->nullable();
            $table->string('numero_celular_familiar')->nullable();
            $table->string('correo_electronico')->nullable();
             $table->text('alerta_contra')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn([
                'direccion_unidad_zona_policia',
                'sub_zona_policia',
                'distrito',
                'circuito_departamento_seccion',
                'subcircuito',
                'funcion_asignada',
                'fecha_presentacion_nueva',
                'novedad',
                'dependencia_destino',
                'detalle_novedad_nueva_unidad',
                'fecha_novedad',
                'tipo_documento',
                'documento_referencia',
                'numero_grupo_trabajo',
                'grupo',
                'modalidad',
                'tipo_sangre',
                'licencia_conducir',
                'numero_celular',
                'numero_celular_familiar',
                'correo_electronico',
                 'alerta_contra',
            ]);
        });
    }
};
