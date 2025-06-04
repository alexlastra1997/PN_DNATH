<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrganicoEfectivoController extends Controller
{
    public function nomenclatura(Request $request, $niveles = null)
    {
        // Aseguramos que niveles sea siempre array
        $niveles = $niveles ? explode('/', urldecode($niveles)) : [];

        $nivelActual = count($niveles);

        // Leer todos los registros
        $registros = DB::table('reporte_organico')->get();

        $proximosNiveles = [];
        $registrosFinales = [];

        foreach ($registros as $registro) {
            $partes = explode('-', trim($registro->nomenclatura, '-'));

            // Comparar niveles navegados
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

            // Si hay siguiente nivel, seguimos
            if (isset($partes[$nivelActual])) {
                $proximosNiveles[] = $partes[$nivelActual];
            } else {
                // No hay más niveles -> registro final
                $registrosFinales[] = $registro;
            }
        }

        $nombresCards = array_count_values($proximosNiveles);

        return view('organico_nomenclatura', [
            'nombresCards' => $nombresCards,
            'niveles' => $niveles,
            'registrosFinales' => $registrosFinales
        ]);
    }


    public function index(Request $request, ...$niveles)
    {
        $search = $request->input('search');
    $filtroCdgPromocion = $request->input('cdg_promocion', []); // array porque puede seleccionar varios
    $filtroProvinciaVive = $request->input('provincia_vive');
    $filtroProvinciaTrabaja = $request->input('provincia_trabaja');

    $usuariosQuery = DB::table('usuarios')
        ->select('cedula', 'grado', 'apellidos_nombres', 'traslado_temporal', 'fecha_pase_actual', 'unidad', 'funcion', 'cdg_promocion', 'provincia_vive', 'provincia_trabaja');

    if ($search) {
        $usuariosQuery->where(function($query) use ($search) {
            $query->where('cedula', 'like', '%' . $search . '%')
                  ->orWhere('apellidos_nombres', 'like', '%' . $search . '%');
        });
    }

    if (!empty($filtroCdgPromocion)) {
        $usuariosQuery->whereIn('cdg_promocion', $filtroCdgPromocion);
    }

    if (!empty($filtroProvinciaVive)) {
        $usuariosQuery->where('provincia_vive', $filtroProvinciaVive);
    }

    if (!empty($filtroProvinciaTrabaja)) {
        $usuariosQuery->where('provincia_trabaja', $filtroProvinciaTrabaja);
    }
        
        $usuarios = $usuariosQuery->get();

        $datos = [];

        $nivelActualString = trim(implode('-', $niveles), '-');

        foreach ($usuarios as $usuario) {
            $trasladoTemporal = $this->getTrasladoMasReciente($usuario->traslado_temporal);

            $fechaTrasladoTemporal = $trasladoTemporal['fecha'] ?? null;
            $detalleTrasladoTemporal = $trasladoTemporal['detalle'] ?? null;

            $fechaPaseActual = $usuario->fecha_pase_actual;

            $fechaCercana = $this->compararFechasCercanaAHoy($fechaTrasladoTemporal, $fechaPaseActual);

            if ($fechaCercana === $fechaTrasladoTemporal) {
                $mejorFecha = $fechaTrasladoTemporal;
                $detalles = $this->extraerNomenclaturaTelegrama($detalleTrasladoTemporal, $fechaTrasladoTemporal);
                $origen = 'traslado';
            } else {
                $mejorFecha = $fechaPaseActual;
                $detalles = [
                    'nomenclatura' => $usuario->unidad ?? '-',
                    'telegrama' => $usuario->funcion ?? '-'
                ];
                $origen = 'pase_actual';
            }

            // Arreglar nomenclatura
        $nomenclaturaCompleta = trim($detalles['nomenclatura'] ?? '-');

        // 1. Quitar el guión final si existe
        $nomenclaturaSinGuionFinal = rtrim($nomenclaturaCompleta, '-');

        // 2. Explode en partes
        $partes = explode('-', $nomenclaturaSinGuionFinal);

        // 3. Trim de cada parte
        $partes = array_map('trim', $partes);

        // 4. Guardar bien la nomenclatura completa
        $nomenclaturaCompletaFormateada = implode('-', $partes);

        // 5. Guardar
        $datos[] = [
            'cedula' => $usuario->cedula,
            'grado' => $usuario->grado,
            'apellidos_nombres' => $usuario->apellidos_nombres,
            'fecha' => $mejorFecha,
            'nomenclatura' => $detalles['nomenclatura'] ?? '-',
            'telegrama' => $detalles['telegrama'] ?? '-',
            'origen' => $origen,
            'nomenclatura_partes' => array_values($partes),
            'nomenclatura_completa' => $nomenclaturaCompletaFormateada,
        ];

        }

        if (!empty($niveles)) {
            $datos = array_filter($datos, function($usuario) use ($nivelActualString) {
                return Str::startsWith($usuario['nomenclatura_completa'], $nivelActualString);
            });
        }

        $proximoNivelIndex = count($niveles);
        $proximosNiveles = [];

        foreach ($datos as $usuario) {
            if (isset($usuario['nomenclatura_partes'][$proximoNivelIndex])) {
                $proximosNiveles[] = $usuario['nomenclatura_partes'][$proximoNivelIndex];
            }
        }

        $nombresCards = [];
        foreach (array_count_values($proximosNiveles) as $nivel => $cantidad) {
            $nombresCards[] = [
                'nombre' => $nivel,
                'cantidad' => $cantidad
            ];
        }

       
        
    $cdgPromociones = DB::table('usuarios')->distinct()->pluck('cdg_promocion')->filter()->unique()->sort()->values();
    $provinciasVive = DB::table('usuarios')->distinct()->pluck('provincia_vive')->filter()->unique()->sort()->values();
    $provinciasTrabaja = DB::table('usuarios')->distinct()->pluck('provincia_trabaja')->filter()->unique()->sort()->values();

        return view('organico_efectivo', compact('datos', 'nombresCards', 'niveles', 'search', 'filtroCdgPromocion', 'filtroProvinciaVive', 'filtroProvinciaTrabaja', 'cdgPromociones', 'provinciasVive', 'provinciasTrabaja'));
    }

    private function getTrasladoMasReciente($traslados)
    {
        if (empty($traslados)) {
            return null;
        }

        $trasladosArray = explode('|', $traslados);

        $trasladosConFechas = [];

        foreach ($trasladosArray as $traslado) {
            $datos = explode('--', $traslado);

            if (isset($datos[0])) {
                $fecha = trim($datos[0]);
                if ($this->isValidDate($fecha)) {
                    $trasladosConFechas[] = [
                        'fecha' => $fecha,
                        'detalle' => trim($traslado)
                    ];
                }
            }
        }

        if (empty($trasladosConFechas)) {
            return null;
        }

        usort($trasladosConFechas, function ($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });

        return $trasladosConFechas[0];
    }

    private function isValidDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function compararFechasCercanaAHoy($fecha1, $fecha2)
    {
        $hoy = strtotime(date('Y-m-d'));

        $diferencia1 = $fecha1 ? abs($hoy - strtotime($fecha1)) : PHP_INT_MAX;
        $diferencia2 = $fecha2 ? abs($hoy - strtotime($fecha2)) : PHP_INT_MAX;

        return ($diferencia1 <= $diferencia2) ? $fecha1 : $fecha2;
    }

    private function extraerNomenclaturaTelegrama($detalleCompleto, $fecha)
    {
        if (!$detalleCompleto) {
            return ['nomenclatura' => '-', 'telegrama' => '-'];
        }

        $detalleSinFecha = str_replace($fecha . ' -- ', '', $detalleCompleto);

        $partes = array_map('trim', explode('--', $detalleSinFecha));

        if (count($partes) >= 2) {
            return [
                'nomenclatura' => $partes[0],
                'telegrama' => $partes[count($partes) - 1]
            ];
        }

        return ['nomenclatura' => '-', 'telegrama' => '-'];
    }
}
