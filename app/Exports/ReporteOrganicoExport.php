<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteOrganicoExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /** @var \Illuminate\Support\Collection */
    protected Collection $rows;

    /** @var array<int,string> */
    protected array $headings;

    /**
     * @param \Illuminate\Support\Collection $rows
     */
    public function __construct(Collection $rows)
    {
        $this->rows = $rows->values();

        // Encabezados: toma las claves de la primera fila, o usa fijos
        if ($this->rows->isNotEmpty()) {
            $first = $this->rows->first();
            $this->headings = array_keys(is_array($first) ? $first : (array) $first);
        } else {
            $this->headings = [
                'Servicio',
                'Nomenclatura',
                'Cargo',
                'Subsistema',
                'Aprobado',
                'Efectivo',
            ];
        }
    }

    /**
     * Devuelve las filas para Excel
     */
    public function collection(): Collection
    {
        return $this->rows->map(function ($row) {
            $rowArr = is_array($row) ? $row : (array) $row;
            return collect($this->headings)
                ->map(fn($h) => $rowArr[$h] ?? '')
                ->values();
        });
    }

    /**
     * Encabezados de la hoja
     */
    public function headings(): array
    {
        return $this->headings;
    }
}
