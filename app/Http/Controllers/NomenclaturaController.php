<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CargosExport;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class NomenclaturaController extends Controller
{
    public function nomenclatura(Request $request, $niveles = null)
    {
        $niveles = $niveles ? explode('/', urldecode($niveles)) : [];
        $nivelActual = count($niveles);

        $registros = DB::table('reporte_organico')
            ->select('id','nomenclatura', 'funcion', 'grado')
            ->get();

        $proximosNiveles = [];
        $registrosCoincidentes = [];

        foreach ($registros as $registro) {
            $partes = explode('-', trim($registro->nomenclatura, '-'));

            $coincide = true;
            foreach ($niveles as $index => $nivelEsperado) {
                if (!isset($partes[$index]) || $partes[$index] !== $nivelEsperado) {
                    $coincide = false;
                    break;
                }
            }

            if (!$coincide) {
                continue;
            }

            if (isset($partes[$nivelActual])) {
                $proximosNiveles[] = $partes[$nivelActual];
            }

            $registrosCoincidentes[] = $registro;
        }

        $nombresCards = array_count_values($proximosNiveles);

        $conteoGrados = collect($registrosCoincidentes)
            ->groupBy('grado')
            ->map(function ($items) {
                return count($items);
            });

        // PAGINACIÓN
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 50;
        $collection = collect($registrosCoincidentes);
        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginator = new LengthAwarePaginator(
            $currentPageItems,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('organico_nomenclatura', [
            'nombresCards' => $nombresCards,
            'niveles' => $niveles,
            'registrosCoincidentes' => $paginator,
            'conteoGrados' => $conteoGrados
        ]);
    }

    public function exportarExcel(Request $request, $niveles = null)
    {
        return Excel::download(new CargosExport($niveles), 'cargos.xlsx');
    }

    public function exportarPDF(Request $request, $niveles = null)
{
    $niveles = $niveles ? explode('/', urldecode($niveles)) : [];
    $nivelActual = count($niveles);

    $registros = DB::table('reporte_organico')
        ->select('id', 'nomenclatura', 'funcion', 'tipo_personal', 'cantidad_ideal', 'grado')
        ->get();

    $usuarios = DB::table('usuarios')
        ->select(DB::raw('TRIM(BOTH "-" FROM TRIM(nomeclatura_efectivo)) AS nomeclatura_efectivo'), 'grado', 'cedula', 'apellidos_nombres')
        ->get()
        ->groupBy(function ($usuario) {
            return $usuario->nomeclatura_efectivo . '|' . $usuario->grado;
        });

    $registrosCoincidentes = [];
    $resagados = [];

    foreach ($registros as $registro) {
        $partes = explode('-', trim($registro->nomenclatura, '-'));
        $coincide = true;
        foreach ($niveles as $index => $nivelEsperado) {
            if (!isset($partes[$index]) || $partes[$index] !== $nivelEsperado) {
                $coincide = false;
                break;
            }
        }

        if ($coincide) {
            $nomenclaturaLimpia = trim(rtrim($registro->nomenclatura, '-'));
            $clave = $nomenclaturaLimpia . '|' . $registro->grado;
            $usuariosCoincidentes = isset($usuarios[$clave]) ? $usuarios[$clave] : collect();
            $registro->cantidad_efectiva = $usuariosCoincidentes->count();
            $registro->usuarios = $usuariosCoincidentes;
            $registro->diferencia = $registro->cantidad_efectiva - $registro->cantidad_ideal;
            $registro->color = $registro->diferencia === 0 ? 'verde' : ($registro->diferencia < 0 ? 'amarillo' : 'rojo');
            $registrosCoincidentes[] = $registro;
        }
    }

    // RESAGADOS
    $usuariosTodos = DB::table('usuarios')
        ->select('cedula', 'apellidos_nombres', 'grado', 'nomeclatura_efectivo')
        ->whereNotNull('nomeclatura_efectivo')
        ->get();

    $cargosEsperados = DB::table('reporte_organico')
        ->select('grado', 'nomenclatura')
        ->where(function($q) use ($niveles) {
            foreach ($niveles as $i => $nivel) {
                $q->whereRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(nomenclatura, '-', ? + 1), '-', -1) = ?", [$i, $nivel]);
            }
        })
        ->get();

    $gradosEsperados = $cargosEsperados->pluck('grado')->unique()->toArray();

    $resagados = $usuariosTodos->filter(function ($usuario) use ($niveles, $gradosEsperados) {
        $partes = explode('-', trim($usuario->nomeclatura_efectivo, '-'));
        foreach ($niveles as $i => $nivel) {
            if (!isset($partes[$i]) || $partes[$i] !== $nivel) {
                return false;
            }
        }
        return !in_array($usuario->grado, $gradosEsperados);
    });

    $pdf = PDF::loadView('exports.cargos-pdf', [
        'registros' => $registrosCoincidentes,
        'resagados' => $resagados
    ]);

    return $pdf->download('cargos.pdf');
}
}