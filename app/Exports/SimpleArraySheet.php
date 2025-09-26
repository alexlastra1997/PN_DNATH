<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class NumericosNomenclaturaEfectivaExport implements WithMultipleSheets
{
    public function __construct(
        public array $zonas,
        public array $zonasSinSz,
        public array $subzonas,
        public array $distritos,
        public array $consolidadoCp
    ) {}

    public function sheets(): array
    {
        return [
            new SimpleArraySheet(
                '01_Zonas',
                ['Zona','Cantidad'],
                $this->zonas
            ),
            new SimpleArraySheet(
                '02_Zonas_sin_SZ',
                ['Zona','Cantidad'],
                $this->zonasSinSz
            ),
            new SimpleArraySheet(
                '03_Subzonas',
                ['Subzona','Cantidad'],
                $this->subzonas
            ),
            new SimpleArraySheet(
                '04_Distritos',
                ['Distrito','Cantidad'],
                $this->distritos
            ),
            new SimpleArraySheet(
                '05_Consolidado',
                ['Zona','Subzona','Distrito','Total','Of. Superiores','Of. Subalternos','Clases y Policías'],
                $this->consolidadoCp
            ),
        ];
    }
}
