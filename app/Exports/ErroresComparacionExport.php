<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ErroresComparacionExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected array $rows;

    public function __construct(array $errores)
    {
        // Mapea a filas simples para Excel
        $this->rows = array_map(function ($e) {
            return [
                $e['fila_excel'] ?? '',
                $e['cedula_excel'] ?? '',
                $e['existe_en_bd'] ?? '',
                $e['nomenclatura_excel'] ?? '',
                $e['nomenclatura_bd'] ?? '',
                $e['motivo'] ?? '',
            ];
        }, $errores);
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Fila (Excel)',
            'Cédula (Excel)',
            'Existe en BD',
            'Nomenclatura (Excel)',
            'Nomenclatura (BD)',
            'Motivo',
        ];
    }
}
