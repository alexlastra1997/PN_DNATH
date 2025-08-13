<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TruequeMultipleExport implements WithMultipleSheets
{
    protected $trueques;
    protected $rezagados;

    public function __construct($trueques, $rezagados)
    {
        $this->trueques = $trueques;
        $this->rezagados = $rezagados;
    }

    public function sheets(): array
    {
        return [
            'trueques_realizados' => new \App\Exports\TruequeExport(array_merge([
                ['cedula','grado','nombre','nomenclatura','funcion','provincia_vive','nueva_nomenclatura','nueva_funcion','cedula_truequeado_con']
            ], array_map('array_values', $this->trueques))),
            'rezagados' => new \App\Exports\TruequeExport(array_merge([
                ['cedula','grado','nombre','nomenclatura','funcion','provincia_vive','nueva_nomenclatura','nueva_funcion','cedula_truequeado_con']
            ], array_map('array_values', $this->rezagados)))
        ];
    }
}