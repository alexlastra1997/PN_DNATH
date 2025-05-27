<?php

namespace App\Imports;

use App\Models\Cargo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CargoImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Cargo([
            'numero' => $row['numero'],
            'cargo' => $row['cargo'],
            'directivo_minimo' => $row['directivo_minimo'],
            'directivo_maximo' => $row['directivo_maximo'],
            'tecnico_minimo' => $row['tecnico_minimo'],
            'tecnico_maximo' => $row['tecnico_maximo'],
        ]);
    }
}


