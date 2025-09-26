<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MapaNdescExport implements WithMultipleSheets
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new \App\Exports\Sheets\ComandantesSheet($this->data);
        $sheets[] = new \App\Exports\Sheets\SubzonaSheet($this->data);

        $estado = $this->data['estadoPorDistrito'] ?? [];
        foreach ($estado as $distrito => $_) {
            if ($distrito === 'SIN DISTRITO') continue;
            $sheets[] = new \App\Exports\Sheets\DistritoSheet($this->data, $distrito);
        }

        return $sheets;
    }
}
