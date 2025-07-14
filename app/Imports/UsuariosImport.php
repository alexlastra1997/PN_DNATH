<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class UsuariosImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {

            // Si la fila está vacía o no tiene cédula, la omitimos
            if (empty($row['cedula'])) {
                continue;
            }

            try {
                DB::table('usuarios')->updateOrInsert(
                    ['cedula' => $row['cedula']],
                    array_merge(
                        $row->toArray(), // copia todos los campos directamente
                        [
                            'fecha_ingreso' => $this->transformDate($row['fecha_ingreso'] ?? null),
                            'fecha_efectivo' => $this->transformDate($row['fecha_efectivo'] ?? null),
                            'fecha_territorio_efectivo' => $this->transformDate($row['fecha_territorio_efectivo'] ?? null),
                            'fecha_pase_anterior' => $this->transformDate($row['fecha_pase_anterior'] ?? null),
                            'fecha_presentacion_nueva' => $this->transformDate($row['fecha_presentacion_nueva'] ?? null),
                            'fecha_novedad' => $this->transformDate($row['fecha_novedad'] ?? null),
                        ]
                    )
                );
            } catch (\Throwable $e) {
                Log::error("Error en la fila {$index}: " . $e->getMessage(), ['row' => $row]);
            }
        }
    }

    public function chunkSize(): int
    {
        return 100; // Ajustable si tu servidor tiene más RAM
    }

    private function transformDate($value)
{
    if (empty($value)) {
        return null;
    }

    if (is_numeric($value)) {
        try {
            return Date::excelToDateTimeObject($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Si ya viene en formato texto válido
    try {
        return date('Y-m-d', strtotime($value));
    } catch (\Throwable $e) {
        return null;
    }
}

}
