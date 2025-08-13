<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class UsuariosImport implements ToModel, WithHeadingRow
{
    protected function convertirFecha($valor)
    {
        try {
            if (is_numeric($valor)) {
                return Carbon::createFromDate(1900, 1, 1)->addDays($valor - 2)->format('Y-m-d');
            }
            return Carbon::parse($valor)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function model(array $row)
    {
        try {
            DB::table('usuarios')->updateOrInsert(
                ['cedula' => trim($row['cedula'])],
                [
                    'grado' => $row['grado'] ?? null,
                    'apellidos_nombres' => $row['apellidos_nombres'] ?? null,
                    'sexo' => $row['sexo'] ?? null,
                    'tipo_personal' => $row['tipo_personal'] ?? null,
                    'antiguedad' => $row['antiguedad'] ?? null,
                    'estado_civil' => $row['estado_civil'] ?? null,
                    'promocion' => $row['promocion'] ?? null,
                    'cdg_promocion' => $row['cdg_promocion'] ?? null,
                    'cuadro_policial' => $row['cuadro_policial'] ?? null,
                    'fecha_ingreso' => $this->convertirFecha($row['fecha_ingreso'] ?? null),
                    'domicilio' => $row['domicilio'] ?? null,
                    'provincia_trabaja' => $row['provincia_trabaja'] ?? null,
                    'provincia_vive' => $row['provincia_vive'] ?? null,
                    'pase_ucp_ccp_cpl' => $row['pase_ucp_ccp_cpl'] ?? null,
                    'capacitacion' => $row['capacitacion'] ?? null,
                    'titulos' => $row['titulos'] ?? null,
                    'titulos_senescyt' => $row['titulos_senescyt'] ?? null,
                    'contrato_estudios' => $row['contrato_estudios'] ?? null,
                    'conyuge_policia_activo' => $row['conyuge_policia_activo'] ?? null,
                    'enf_catast_sp' => $row['enf_catast_sp'] ?? null,
                    'enf_catast_conyuge_hijos' => $row['enf_catast_conyuge_hijos'] ?? null,
                    'discapacidad_sp' => $row['discapacidad_sp'] ?? null,
                    'discapacidad_conyuge_hijos' => $row['discapacidad_conyuge_hijos'] ?? null,
                    'hijos_menor_igual_18' => $row['hijos_menor_igual_18'] ?? null,
                    'alertas' => $row['alertas'] ?? null,
                    'meritos' => $row['meritos'] ?? null,
                    'num_demerito' => $row['num_demerito'] ?? null,
                    'novedad_situacion' => $row['novedad_situacion'] ?? null,
                    'observacion_tenencia' => $row['observacion_tenencia'] ?? null,
                    'alertas_problemas_salud' => $row['alertas_problemas_salud'] ?? null,
                    'FaseMaternidadUDGA' => $row['FaseMaternidadUDGA'] ?? null,
                    'historico_pases' => $row['historico_pases'] ?? null,
                    'traslado_temporal' => $row['traslado_temporal'] ?? null,
                    'traslado_eventual' => $row['traslado_eventual'] ?? null,
                    'comisiones' => $row['comisiones'] ?? null,
                    'fechaPresentacion' => $this->convertirFecha($row['fechapresentacion'] ?? null),
                    'ultimo_pase' => $row['ultimo_pase'] ?? null,
                    'funcion_origen' => $row['funcion_origen'] ?? null,
                    'fecha_presentacion_traslado' => $this->convertirFecha($row['fecha_presentacion_traslado'] ?? null),
                    'numeroDias' => $row['numerodias'] ?? null,
                    'nomenclatura' => $row['nomenclatura'] ?? null,
                    'funcion_tras' => $row['funcion_tras'] ?? null,
                    'descripcion' => $row['descripcion'] ?? null,
                    'pase_anterior' => $row['pase_anterior'] ?? null,
                    'fecha_pase_anterior' => $this->convertirFecha($row['fecha_pase_anterior'] ?? null),
                    'designaciones' => $row['designaciones'] ?? null,
                    'maternidad' => $row['maternidad'] ?? null,
                    'proyeccion_licencia' => $row['proyeccion_licencia'] ?? null,
                    'dmq' => $row['dmq'] ?? null,
                    'dmg' => $row['dmg'] ?? null,
                    'azuay' => $row['azuay'] ?? null,
                    'bolivar' => $row['bolivar'] ?? null,
                    'canar' => $row['canar'] ?? null,
                    'carchi' => $row['carchi'] ?? null,
                    'cotopaxi' => $row['cotopaxi'] ?? null,
                    'chimborazo' => $row['chimborazo'] ?? null,
                    'el_oro' => $row['el_oro'] ?? null,
                    'esmeraldas' => $row['esmeraldas'] ?? null,
                    'guayas' => $row['guayas'] ?? null,
                    'imbabura' => $row['imbabura'] ?? null,
                    'loja' => $row['loja'] ?? null,
                    'los_rios' => $row['los_rios'] ?? null,
                    'manabi' => $row['manabi'] ?? null,
                    'morona' => $row['morona'] ?? null,
                    'napo' => $row['napo'] ?? null,
                    'pastaza' => $row['pastaza'] ?? null,
                    'pichincha' => $row['pichincha'] ?? null,
                    'tungurahua' => $row['tungurahua'] ?? null,
                    'zamora' => $row['zamora'] ?? null,
                    'galapagos' => $row['galapagos'] ?? null,
                    'sucumbios' => $row['sucumbios'] ?? null,
                    'orellana' => $row['orellana'] ?? null,
                    's_domingo' => $row['s_domingo'] ?? null,
                    's_elena' => $row['s_elena'] ?? null,
                    'exterior' => $row['exterior'] ?? null,
                    'fecha_efectiva' => $this->convertirFecha($row['fecha_efectiva'] ?? null),
                    'nomenclatura_efectiva' => $row['nomenclatura_efectiva'] ?? null,
                    'funcion_efectiva' => $row['funcion_efectiva'] ?? null,
                    'dias_efectivos' => $row['dias_efectivos'] ?? null,
                    'estado_efectivo' => $row['estado_efectivo'] ?? null,
                    'direccion_unidad_zona_policia' => $row['direccion_unidad_zona_policia'] ?? null,
                    'sub_zona_policia' => $row['sub_zona_policia'] ?? null,
                    'distrito' => $row['distrito'] ?? null,
                    'circuito_departamento_seccion' => $row['circuito_departamento_seccion'] ?? null,
                    'subcircuito' => $row['subcircuito'] ?? null,
                    'funcion_asignada' => $row['funcion_asignada'] ?? null,
                    'fecha_presentacion_nueva' => $this->convertirFecha($row['fecha_presentacion_nueva'] ?? null),
                    'novedad' => $row['novedad'] ?? null,
                    'dependencia_destino' => $row['dependencia_destino'] ?? null,
                    'detalle_novedad_nueva_unidad' => $row['detalle_novedad_nueva_unidad'] ?? null,
                    'fecha_novedad' => $this->convertirFecha($row['fecha_novedad'] ?? null),
                    'tipo_documento' => $row['tipo_documento'] ?? null,
                    'documento_referencia' => $row['documento_referencia'] ?? null,
                    'numero_grupo_trabajo' => $row['numero_grupo_trabajo'] ?? null,
                    'grupo' => $row['grupo'] ?? null,
                    'modalidad' => $row['modalidad'] ?? null,
                    'tipo_sangre' => $row['tipo_sangre'] ?? null,
                    'licencia_conducir' => $row['licencia_conducir'] ?? null,
                    'numero_celular' => $row['numero_celular'] ?? null,
                    'numero_celular_familiar' => $row['numero_celular_familiar'] ?? null,
                    'correo_electronico' => $row['correo_electronico'] ?? null,
                    'alerta_contra' => $row['alerta_contra'] ?? null,
                ]
            );
            Log::info("✔️ Importado: " . $row['cedula']);
        } catch (\Exception $e) {
            Log::error("❌ Error con " . $row['cedula'] . ": " . $e->getMessage());
        }

        return null;
    }
}
