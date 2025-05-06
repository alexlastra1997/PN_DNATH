<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class UsuariosImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $data = $row->toArray();

            // Transformar campos de fecha
            $data['fechanacimiento']        = $this->transformDate($data['fechanacimiento'] ?? null);
            $data['fechaasignacion']        = $this->transformDate($data['fechaasignacion'] ?? null);
            $data['fechaingreso']           = $this->transformDate($data['fechaingreso'] ?? null);
            $data['fechaascenso']           = $this->transformDate($data['fechaascenso'] ?? null);
            $data['fechapasedesde']         = $this->transformDate($data['fechapasedesde'] ?? null);
            $data['fechapresentacion']      = $this->transformDate($data['fechapresentacion'] ?? null);
            $data['fechanovedaddesde']      = $this->transformDate($data['fechanovedaddesde'] ?? null);
            $data['fechanovedadhasta']      = $this->transformDate($data['fechanovedadhasta'] ?? null);
            $data['periodomaternidad']      = $this->transformDate($data['periodomaternidad'] ?? null);

            // Insertar fila en la base de datos
            DB::table('usuarios')->insert($data);
        }
    }

    public function chunkSize(): int
    {
        return 60000;
    }

    private function transformDate($value)
    {
        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } elseif (strtotime($value)) {
                return date('Y-m-d', strtotime($value));
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}


