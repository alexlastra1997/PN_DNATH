<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CargosExport implements WithMultipleSheets
{
    protected $niveles;

    public function __construct($niveles)
    {
        $this->niveles = $niveles ? explode('/', urldecode($niveles)) : [];
    }

    public function sheets(): array
    {
        return [
            new CargosSheetExport($this->niveles),
            new ResagadosSheetExport($this->niveles),
        ];
    }
}
