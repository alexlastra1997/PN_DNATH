<?php

namespace App\Imports;

use App\Models\ReporteOrganico;
use Maatwebsite\Excel\Concerns\ToModel;

class ReporteOrganicoImport implements ToModel
{
    public function model(array $row)
    {
        // Saltar encabezado si detectas strings como "Unidad"
        if (strtolower($row[0]) === 'unidad') {
            return null;
        }

        return new ReporteOrganico([
            'unidad'          => $row[0],
            'nomenclatura'    => $row[1],
            'grado'           => $row[2],
            'funcion'         => $row[3],
            'tipo_personal'   => $row[4],
            'cantidad_ideal'  => (int) $row[5],
            'cantidad_real'   => (int) $row[6],
            'diferencia'      => (int) $row[7],
        ]);
    }
}

