<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResagadosSheetExport implements FromCollection, WithHeadings
{
    protected $niveles;

    public function __construct($niveles)
    {
        $this->niveles = $niveles;
    }

    public function collection()
    {
        $query = DB::table('usuarios')
            ->select('cedula', 'apellidos_nombres', 'grado', 'nomeclatura_efectivo')
            ->whereNotNull('nomeclatura_efectivo');

        foreach ($this->niveles as $i => $nivel) {
            $query->whereRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(nomeclatura_efectivo, '-', ? + 1), '-', -1) = ?", [$i, $nivel]);
        }

        $usuarios = $query->get();

        $cargosEsperados = DB::table('reporte_organico')
            ->select('grado', 'nomenclatura')
            ->where(function($q) {
                foreach ($this->niveles as $i => $nivel) {
                    $q->whereRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(nomenclatura, '-', ? + 1), '-', -1) = ?", [$i, $nivel]);
                }
            })
            ->get();

        $gradosEsperados = $cargosEsperados->pluck('grado')->unique()->toArray();

        $resagados = $usuarios->filter(function ($usuario) use ($gradosEsperados) {
            return !in_array($usuario->grado, $gradosEsperados);
        });

        return collect($resagados)->map(function ($item) {
            $item = (object) $item;
            return [
                'Cédula' => $item->cedula,
                'Nombre' => $item->apellidos_nombres,
                'Grado' => $item->grado,
                'Nomenclatura' => $item->nomeclatura_efectivo,
            ];
        });
    }

    public function headings(): array
    {
        return ['Cédula', 'Nombre', 'Grado', 'Nomenclatura'];
    }
}
