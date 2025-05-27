<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;

class UsuariosImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            DB::table('usuarios')->insert([
                'cedula' => $row['cedula'],
                'grado' => $row['grado'],
                'apellidos_nombres' => $row['apellidos_nombres'],
                'sexo' => $row['sexo'],
                'tipo_personal' => $row['tipo_personal'],
                'antiguedad' => $row['antiguedad'],
                'unidad' => $row['unidad'],
                'funcion' => $row['funcion'],
                'estado_civil' => $row['estado_civil'],
                'promocion' => $row['promocion'],
                'cdg_promocion' => $row['cdg_promocion'],
                'fecha_ingreso' => $row['fecha_ingreso'],
                'unidad_anterior' => $row['unidad_anterior'],
                'fecha_pase_anterior' => $row['fecha_pase_anterior'],
                'tiempo_unidad_anterior' => $row['tiempo_unidad_anterior'],
                'fecha_pase_actual' => $row['fecha_pase_actual'],
                'tiempo_ultimo_pase' => $row['tiempo_ultimo_pase'],
                'fecha_actual' => $row['fecha_actual'],
                'tiempo_pase_formula' => $row['tiempo_pase_formula'],
                'fecha_presentacion' => $row['fecha_presentacion'],
                'servicio_grupal' => $row['servicio_grupal'],
                'domicilio' => $row['domicilio'],
                'provincia_trabaja' => $row['provincia_trabaja'],
                'provincia_vive' => $row['provincia_vive'],
                'pase_ucp_ccp_cpl' => $row['pase_ucp_ccp_cpl'],
                'cuadro_policial' => $row['cuadro_policial'],
                'capacitacion' => $row['capacitacion'],
                'titulos' => $row['titulos'],
                'titulos_senescyt' => $row['titulos_senescyt'],
                'contrato_estudios' => $row['contrato_estudios'],
                'conyuge_policia_activo' => $row['conyuge_policia_activo'],
                'enf_catast_sp' => $row['enf_catast_sp'],
                'enf_catast_conyuge_hijos' => $row['enf_catast_conyuge_hijos'],
                'discapacidad_sp' => $row['discapacidad_sp'],
                'discapacidad_conyuge_hijos' => $row['discapacidad_conyuge_hijos'],
                'hijos_menor_igual_18' => $row['hijos_menor_igual_18'],
                'alertas' => $row['alertas'],
                'meritos' => $row['meritos'],
                'num_demerito' => $row['num_demerito'],
                'novedad_situacion' => $row['novedad_situacion'],
                'historico_pases' => $row['historico_pases'],
                'designaciones' => $row['designaciones'],
                'maternidad' => $row['maternidad'],
                'proyeccion_licencia' => $row['proyeccion_licencia'],
                'dmq' => $row['dmq'],
                'dmg' => $row['dmg'],
                'azuay' => $row['azuay'],
                'bolivar' => $row['bolivar'],
                'canar' => $row['canar'],
                'carchi' => $row['carchi'],
                'cotopaxi' => $row['cotopaxi'],
                'chimborazo' => $row['chimborazo'],
                'el_oro' => $row['el_oro'],
                'esmeraldas' => $row['esmeraldas'],
                'guayas' => $row['guayas'],
                'imbabura' => $row['imbabura'],
                'loja' => $row['loja'],
                'los_rios' => $row['los_rios'],
                'manabi' => $row['manabi'],
                'morona' => $row['morona'],
                'napo' => $row['napo'],
                'pastaza' => $row['pastaza'],
                'pichincha' => $row['pichincha'],
                'tungurahua' => $row['tungurahua'],
                'zamora' => $row['zamora'],
                'galapagos' => $row['galapagos'],
                'sucumbios' => $row['sucumbios'],
                'orellana' => $row['orellana'],
                's_domingo' => $row['s_domingo'],
                's_elena' => $row['s_elena'],
                'exterior' => $row['exterior'],
            ]);
        }
    }

    public function chunkSize(): int
    {
        return 1000; // Puedes ajustarlo según la capacidad de tu servidor
    }
}
