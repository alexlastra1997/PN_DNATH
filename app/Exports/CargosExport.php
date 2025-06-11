<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CargosExport implements FromCollection, WithHeadings, WithStyles
{
    protected $niveles;

    public function __construct($niveles)
    {
        $this->niveles = $niveles ? explode('/', urldecode($niveles)) : [];
    }

    public function collection()
    {
        $nivelActual = count($this->niveles);

        // 1. Traemos todos los registros de reporte_organico
        $registros = DB::table('reporte_organico')
            ->select('id', 'nomenclatura', 'funcion', 'tipo_personal', 'cantidad_ideal', 'grado')
            ->get();

        // 2. Traemos todos los usuarios una sola vez
        $usuarios = DB::table('usuarios')
            ->select(
                DB::raw('TRIM(BOTH "-" FROM TRIM(nomeclatura_efectivo)) AS nomenclatura_efectivo'),
                'grado',
                'apellidos_nombres'
            )
            ->get()
            ->groupBy(function ($usuario) {
                return $usuario->nomenclatura_efectivo . '|' . $usuario->grado; // clave compuesta nomenclatura|grado
            });

        $registrosCoincidentes = [];

        foreach ($registros as $registro) {
            $partes = explode('-', trim($registro->nomenclatura, '-'));
            $coincide = true;
            foreach ($this->niveles as $index => $nivelEsperado) {
                if (!isset($partes[$index]) || $partes[$index] !== $nivelEsperado) {
                    $coincide = false;
                    break;
                }
            }
            if ($coincide) {
                $nomenclaturaLimpia = trim(rtrim($registro->nomenclatura, '-'));

                if (!empty($nomenclaturaLimpia)) {
                    $clave = $nomenclaturaLimpia . '|' . $registro->grado;
                    $usuariosCoincidentes = isset($usuarios[$clave]) ? $usuarios[$clave] : collect();
                    $efectivo = $usuariosCoincidentes->count();
                    $nombres = $usuariosCoincidentes->pluck('apellidos_nombres')->implode(', ');
                } else {
                    $efectivo = 0;
                    $nombres = '';
                }

                $registro->efectivo = $efectivo;
                $registro->nombres = $nombres;

                if (is_numeric($registro->cantidad_ideal)) {
                    $diferencia = $efectivo - $registro->cantidad_ideal;
                    if ($diferencia == 0) {
                        $registro->estado = 'Completo';
                    } elseif ($diferencia < 0) {
                        $registro->estado = 'Faltan ' . abs($diferencia);
                    } else {
                        $registro->estado = 'Sobran ' . $diferencia;
                    }
                } else {
                    $registro->estado = 'Sin dato';
                }

                $registrosCoincidentes[] = [
                    $registro->nomenclatura,
                    $registro->funcion,
                    $registro->tipo_personal,
                    $registro->grado,
                    $registro->cantidad_ideal,
                    $registro->efectivo,
                    $registro->estado,
                    $registro->nombres, // <-- AÑADIMOS LOS NOMBRES
                ];
            }
        }

        return new Collection($registrosCoincidentes);
    }



    public function headings(): array
    {
        return [
            'Nomenclatura',
            'Función',
            'Tipo Personal',
            'Grado',
            'Cantidad Ideal',
            'Efectivo',
            'Estado',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $estado = $sheet->getCell('G' . $row)->getValue();

            if (stripos($estado, 'Completo') !== false) {
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'C6F6D5'] // Verde
                    ]
                ]);
            } elseif (stripos($estado, 'Faltan') !== false) {
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'FEFCBF'] // Amarillo
                    ]
                ]);
            } elseif (stripos($estado, 'Sobran') !== false) {
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'FEB2B2'] // Rojo
                    ]
                ]);
            }
        }
    }
}
