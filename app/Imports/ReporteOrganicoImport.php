<?php
namespace App\Imports;

use App\Models\ReporteOrganico;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ReporteOrganicoImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new ReporteOrganico([
            'servicio_organico'       => $row['servicio_organico'] ?? null,
            'nomenclatura_organico'   => $row['nomenclatura_organico'] ?? null,
            'cargo_organico'          => $row['cargo_organico'] ?? null,
            'grado_organico'          => $row['grado_organico'] ?? null,
            'personal_organico'       => $row['personal_organico'] ?? null,
            'numero_organico_ideal'   => $row['numero_organico_ideal'] ?? null,
            'subsistema'              => $row['subsistema'] ?? null,
        ]);
    }
}

