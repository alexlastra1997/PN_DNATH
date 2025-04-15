<?php


namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsuariosImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function collection(Collection $rows)
{
    foreach ($rows as $row) {
        $data = $row->toArray();

         // Verificamos si la columna de fecha está presente y es un número
         if (isset($data['fecha_ingreso']) && is_numeric($data['fecha_ingreso'])) {
            // Convertimos el número de Excel a una fecha
            $data['fecha_ingreso'] = Carbon::createFromFormat('Y-m-d', '1900-01-01')
                ->addDays($data['fecha_ingreso'] - 2)  // Restamos 2 porque Excel comienza con 1/1/1900 como 1
                ->format('Y-m-d');  // Formato de fecha adecuado para la base de datos
        }

        // Verificamos si la columna de fecha está presente y es un número
        if (isset($data['fecha_pase_anterior']) && is_numeric($data['fecha_pase_anterior'])) {
            // Convertimos el número de Excel a una fecha
            $data['fecha_pase_anterior'] = Carbon::createFromFormat('Y-m-d', '1900-01-01')
                ->addDays($data['fecha_pase_anterior'] - 2)  // Restamos 2 porque Excel comienza con 1/1/1900 como 1
                ->format('Y-m-d');  // Formato de fecha adecuado para la base de datos
        }

        // Verificamos si la columna de fecha está presente y es un número
        if (isset($data['fecha_pase_actual']) && is_numeric($data['fecha_pase_actual'])) {
            // Convertimos el número de Excel a una fecha
            $data['fecha_pase_actual'] = Carbon::createFromFormat('Y-m-d', '1900-01-01')
                ->addDays($data['fecha_pase_actual'] - 2)  // Restamos 2 porque Excel comienza con 1/1/1900 como 1
                ->format('Y-m-d');  // Formato de fecha adecuado para la base de datos
        }

        // Verificamos si la columna de fecha está presente y es un número
        if (isset($data['fecha_actual']) && is_numeric($data['fecha_actual'])) {
            // Convertimos el número de Excel a una fecha
            $data['fecha_actual'] = Carbon::createFromFormat('Y-m-d', '1900-01-01')
                ->addDays($data['fecha_actual'] - 2)  // Restamos 2 porque Excel comienza con 1/1/1900 como 1
                ->format('Y-m-d');  // Formato de fecha adecuado para la base de datos
        }

        // Verificamos si la columna de fecha está presente y es un número
        if (isset($data['fecha_presentacion']) && is_numeric($data['fecha_presentacion'])) {
            // Convertimos el número de Excel a una fecha
            $data['fecha_presentacion'] = Carbon::createFromFormat('Y-m-d', '1900-01-01')
                ->addDays($data['fecha_presentacion'] - 2)  // Restamos 2 porque Excel comienza con 1/1/1900 como 1
                ->format('Y-m-d');  // Formato de fecha adecuado para la base de datos
        }


        // Corregimos el nombre de la columna si viene mal escrita
        if (isset($data['registra_obser0n_tenencia'])) {
            $data['registra_observacion_tenencia'] = $data['registra_obser0n_tenencia'];
            unset($data['registra_obser0n_tenencia']);
        }

        DB::table('usuarios')->insert($data);
    }
}

    public function chunkSize(): int
    {
        return 60000;
    }
}

