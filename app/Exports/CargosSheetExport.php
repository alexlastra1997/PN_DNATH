<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CargosSheetExport implements FromCollection, WithHeadings
{
    protected $niveles;

    public function __construct($niveles)
    {
        $this->niveles = $niveles;
    }

    public function collection()
    {
        $cargos = DB::table('reporte_organico')
            ->select('nomenclatura', 'grado', 'funcion', 'cantidad_ideal')
            ->where(function ($q) {
                foreach ($this->niveles as $i => $nivel) {
                    $q->whereRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(nomenclatura, '-', ? + 1), '-', -1) = ?", [$i, $nivel]);
                }
            })
            ->get();

        $usuarios = DB::table('usuarios')
            ->select('grado', 'apellidos_nombres', DB::raw("TRIM(BOTH '-' FROM TRIM(nomeclatura_efectivo)) AS nomeclatura_efectivo"))
            ->whereNotNull('nomeclatura_efectivo')
            ->get()
            ->groupBy(function ($usuario) {
                return $usuario->nomeclatura_efectivo . '|' . $usuario->grado;
            });

        return collect($cargos)->map(function ($item) use ($usuarios) {
            $nomenclaturaLimpia = trim(rtrim($item->nomenclatura, '-'));
            $clave = $nomenclaturaLimpia . '|' . $item->grado;
            $usuariosCoincidentes = isset($usuarios[$clave]) ? $usuarios[$clave] : collect();
            $cantidadEfectiva = $usuariosCoincidentes->count();
            $diferencia = $cantidadEfectiva - $item->cantidad_ideal;

            if ($diferencia === 0) {
                $color = 'verde';
            } elseif ($diferencia < 0) {
                $color = 'amarillo';
            } else {
                $color = 'rojo';
            }

            return [
                'Nomenclatura' => $item->nomenclatura,
                'Grado' => $item->grado,
                'Función' => $item->funcion,
                'Cantidad Ideal' => $item->cantidad_ideal,
                'Cantidad Efectiva' => $cantidadEfectiva,
                'Diferencia' => $diferencia,
                'Color' => $color,
                'Nombres' => $usuariosCoincidentes->pluck('apellidos_nombres')->implode(', '),
            ];
        });
    }

    public function headings(): array
    {
        return ['Nomenclatura', 'Grado', 'Función', 'Cantidad Ideal', 'Cantidad Efectiva', 'Diferencia', 'Color', 'Nombres'];
    }
}