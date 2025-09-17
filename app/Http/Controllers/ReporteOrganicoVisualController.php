<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteOrganicoExport;
use App\Exports\ResumenOrganicoExport;

class ReporteOrganicoVisualController extends Controller
{
    public function index(Request $request)
    {
        // ====== BASE ======
        $base = DB::table('reporte_organico as ro');

        // ------------ Filtros múltiples (texto taggeable con LIKE OR) ------------
        $servicios = array_values(array_filter((array) $request->input('servicio', []), fn($v) => trim($v) !== ''));
        if ($servicios) {
            $base->where(function ($w) use ($servicios) {
                foreach ($servicios as $v) {
                    $w->orWhere('ro.servicio_organico', 'like', '%' . trim($v) . '%');
                }
            });
        }

        $nomenclaturas = array_values(array_filter((array) $request->input('nomenclatura', []), fn($v) => trim($v) !== ''));
        if ($nomenclaturas) {
            $base->where(function ($w) use ($nomenclaturas) {
                foreach ($nomenclaturas as $v) {
                    $w->orWhere('ro.nomenclatura_organico', 'like', '%' . trim($v) . '%');
                }
            });
        }

        $cargos = array_values(array_filter((array) $request->input('cargo', []), fn($v) => trim($v) !== ''));
        if ($cargos) {
            $base->where(function ($w) use ($cargos) {
                foreach ($cargos as $v) {
                    $w->orWhere('ro.cargo_organico', 'like', '%' . trim($v) . '%');
                }
            });
        }

        // ------------ Subsistema (exacto, múltiples) ------------
        $subsistemas = array_values(array_filter((array) $request->input('subsistema', []), fn($v) => trim($v) !== ''));
        if ($subsistemas) {
            $base->where(function ($w) use ($subsistemas) {
                foreach ($subsistemas as $s) {
                    $w->orWhereRaw('TRIM(UPPER(ro.subsistema)) = ?', [mb_strtoupper(trim($s))]);
                }
            });
        }

        // ------------ Grado orgánico (múltiples tokens contra lista separada por comas) ------------
        $grados = array_values(array_filter((array) $request->input('grado_organico', []), fn($v) => trim($v) !== ''));
        if ($grados) {
            $grados = array_map(fn($g) => mb_strtoupper(trim($g)), $grados);
            $base->where(function ($w) use ($grados) {
                foreach ($grados as $g) {
                    // Coincidencia exacta de token en lista separada por comas
                    $pattern = '(^|,)[[:space:]]*' . preg_quote($g, '/') . '([[:space:]]*,|$)';
                    $w->orWhereRaw("UPPER(ro.grado_organico) REGEXP ?", [$pattern]);
                }
            });
        }

        // ------------ Estado (VACANTE/COMPLETO/EXCEDIDO) como OR entre los seleccionados ------------
        $estados = array_values(array_filter((array) $request->input('estado', []), fn($v) => in_array($v, ['VACANTE','COMPLETO','EXCEDIDO'], true)));
        if ($estados) {
            $efectivoExpr = DB::raw('(SELECT COUNT(*)
                                      FROM usuarios u
                                      WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                                        AND u.funcion_efectiva      = ro.cargo_organico)');
            $base->where(function ($w) use ($estados, $efectivoExpr) {
                foreach ($estados as $estado) {
                    if ($estado === 'VACANTE')   $w->orWhereRaw("{$efectivoExpr} < ro.numero_organico_ideal");
                    if ($estado === 'COMPLETO')  $w->orWhereRaw("{$efectivoExpr} = ro.numero_organico_ideal");
                    if ($estado === 'EXCEDIDO')  $w->orWhereRaw("{$efectivoExpr} > ro.numero_organico_ideal");
                }
            });
        }

        // ====== Totales ======
        $totales = (clone $base)
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
                SELECT COUNT(*) FROM usuarios u
                WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                  AND u.funcion_efectiva      = ro.cargo_organico
            )),0) as total_efectivo')
            ->first();

        // ====== Listado principal + bandera no_cumplen_grado ======
        $datos = (clone $base)
            ->select(
                'ro.servicio_organico',
                'ro.nomenclatura_organico',
                'ro.cargo_organico',
                'ro.grado_organico',
                DB::raw('ro.numero_organico_ideal as organico_aprobado'),
                DB::raw('(SELECT COUNT(*) FROM usuarios u
                          WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                            AND u.funcion_efectiva      = ro.cargo_organico) as organico_efectivo')
            )
            ->selectRaw("(SELECT COUNT(*)
                          FROM usuarios u
                          WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                            AND u.funcion_efectiva      = ro.cargo_organico
                            AND ro.grado_organico IS NOT NULL
                            AND TRIM(ro.grado_organico) <> ''
                            AND (
                                  u.grado IS NULL
                                  OR u.grado = ''
                                  OR NOT (
                                       UPPER(ro.grado_organico)
                                       REGEXP CONCAT('(^|,)[[:space:]]*', UPPER(TRIM(u.grado)), '([[:space:]]*,|$)')
                                  )
                                )
                        ) AS no_cumplen_grado")
            ->paginate(50)
            ->withQueryString();

        // ====== Resumen por SUBSISTEMA ======
        $efectivoExprSql = '(SELECT COUNT(*) FROM usuarios u
                             WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                               AND u.funcion_efectiva      = ro.cargo_organico)';

        $statsSubsistema = (clone $base)
            ->selectRaw("COALESCE(NULLIF(TRIM(ro.subsistema),''),'SIN SUBSISTEMA') as subsistema")
            ->selectRaw("SUM(ro.numero_organico_ideal) as total_aprobado")
            ->selectRaw("SUM($efectivoExprSql) as total_efectivo")
            ->selectRaw("SUM(CASE WHEN ($efectivoExprSql) <  ro.numero_organico_ideal THEN 1 ELSE 0 END) as cargos_vacantes")
            ->selectRaw("SUM(CASE WHEN ($efectivoExprSql) =  ro.numero_organico_ideal THEN 1 ELSE 0 END) as cargos_completos")
            ->selectRaw("SUM(CASE WHEN ($efectivoExprSql) >  ro.numero_organico_ideal THEN 1 ELSE 0 END) as cargos_excedidos")
            ->selectRaw("SUM(($efectivoExprSql) - ro.numero_organico_ideal) as diferencia_total")
            ->groupBy('subsistema')
            ->orderBy('subsistema')
            ->get();

        // ====== Opciones para selectores ======
        $opcionesServicio      = $this->distinctOptions('servicio_organico');
        $opcionesNomenclatura  = $this->distinctOptions('nomenclatura_organico');
        $opcionesCargo         = $this->distinctOptions('cargo_organico');
        $opcionesGrado         = $this->buildGradoOptions();
        $opcionesSubsistema    = $this->buildSubsistemaOptions();

        return view('reporte_organico.visualizador', compact(
            'datos','totales',
            'opcionesServicio','opcionesNomenclatura','opcionesCargo',
            'opcionesGrado','opcionesSubsistema',
            'statsSubsistema'
        ));
    }

    public function buscarNomenclatura(Request $request)
    {
        $valor = $request->input('query');

        $datos = DB::table('reporte_organico')
            ->select(
                'servicio_organico',
                'nomenclatura_organico',
                'cargo_organico',
                DB::raw('SUM(numero_organico_ideal) as organico_aprobado')
            )
            ->where('nomenclatura_organico', 'LIKE', "%$valor%")
            ->groupBy('servicio_organico', 'nomenclatura_organico', 'cargo_organico')
            ->get();

        return response()->json([
            'html' => View::make('reporte_organico.partials.tabla', ['datos' => $datos])->render()
        ]);
    }

    public function ocupantes(Request $request)
    {
        $nomenclatura = $request->input('nomenclatura');
        $cargo        = $request->input('cargo');

        $ocupantes = DB::table('usuarios')
            ->select('cedula','grado','apellidos_nombres','estado_efectivo')
            ->where('nomenclatura_efectiva', $nomenclatura)
            ->where('funcion_efectiva',      $cargo)
            ->orderBy('grado')
            ->orderBy('apellidos_nombres')
            ->get();

        $infoCargo = DB::table('reporte_organico as ro')
            ->select(
                'ro.servicio_organico',
                'ro.nomenclatura_organico',
                'ro.cargo_organico',
                'ro.grado_organico',
                'ro.personal_organico',
                'ro.numero_organico_ideal'
            )
            ->where('ro.nomenclatura_organico', $nomenclatura)
            ->where('ro.cargo_organico',        $cargo)
            ->orderBy('ro.servicio_organico')
            ->get();

        $gradosPermitidos = [];
        foreach ($infoCargo as $fila) {
            $tokens = preg_split('/[,;]+/u', (string)($fila->grado_organico ?? ''), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($tokens as $t) {
                $t = mb_strtoupper(trim($t));
                if ($t !== '') $gradosPermitidos[$t] = true;
            }
        }
        $gradosPermitidos = array_keys($gradosPermitidos);

        return view('reporte_organico.ocupantes', compact(
            'ocupantes','nomenclatura','cargo','infoCargo','gradosPermitidos'
        ));
    }

    public function exportarExcel(Request $request)
    {
        return Excel::download(new ReporteOrganicoExport($request), 'reporte_organico_filtrado.xlsx');
    }

    public function obtenerEstadisticas(Request $request)
    {
        $query = DB::table('reporte_organico as ro')
            ->select(
                DB::raw('(SELECT COUNT(*) FROM usuarios u
                          WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                            AND u.funcion_efectiva      = ro.cargo_organico) as organico_efectivo'),
                'ro.numero_organico_ideal as organico_aprobado'
            );

        // Reutiliza la misma lógica de filtros múltiples:
        $servicios = array_values(array_filter((array) $request->input('servicio', []), fn($v) => trim($v) !== ''));
        if ($servicios) {
            $query->where(function ($w) use ($servicios) {
                foreach ($servicios as $v) $w->orWhere('ro.servicio_organico','like','%'.trim($v).'%');
            });
        }
        $nomenclaturas = array_values(array_filter((array) $request->input('nomenclatura', []), fn($v) => trim($v) !== ''));
        if ($nomenclaturas) {
            $query->where(function ($w) use ($nomenclaturas) {
                foreach ($nomenclaturas as $v) $w->orWhere('ro.nomenclatura_organico','like','%'.trim($v).'%');
            });
        }
        $cargos = array_values(array_filter((array) $request->input('cargo', []), fn($v) => trim($v) !== ''));
        if ($cargos) {
            $query->where(function ($w) use ($cargos) {
                foreach ($cargos as $v) $w->orWhere('ro.cargo_organico','like','%'.trim($v).'%');
            });
        }
        $subsistemas = array_values(array_filter((array) $request->input('subsistema', []), fn($v) => trim($v) !== ''));
        if ($subsistemas) {
            $query->where(function ($w) use ($subsistemas) {
                foreach ($subsistemas as $s) $w->orWhereRaw('TRIM(UPPER(ro.subsistema)) = ?', [mb_strtoupper(trim($s))]);
            });
        }
        $grados = array_values(array_filter((array) $request->input('grado_organico', []), fn($v) => trim($v) !== ''));
        if ($grados) {
            $grados = array_map(fn($g) => mb_strtoupper(trim($g)), $grados);
            $query->where(function ($w) use ($grados) {
                foreach ($grados as $g) {
                    $pattern = '(^|,)[[:space:]]*' . preg_quote($g, '/') . '([[:space:]]*,|$)';
                    $w->orWhereRaw("UPPER(ro.grado_organico) REGEXP ?", [$pattern]);
                }
            });
        }

        $datos = $query->get();

        $vacantes = 0; $completos = 0; $excedidos = 0;
        foreach ($datos as $item) {
            if ($item->organico_efectivo < $item->organico_aprobado) $vacantes++;
            elseif ($item->organico_efectivo == $item->organico_aprobado) $completos++;
            else $excedidos++;
        }

        return response()->json([
            'vacantes'  => $vacantes,
            'completos' => $completos,
            'excedidos' => $excedidos
        ]);
    }

    public function exportResumenXlsx(Request $request)
    {
        $filename = 'resumen_organico_' . now()->format('Ymd_His') . '.xlsx';
        $filters = $request->only(['servicio','nomenclatura','cargo','estado','grado_organico','subsistema']);
        return Excel::download(new ResumenOrganicoExport($filters), $filename);
    }

    /** Distintos valores para un campo de reporte_organico */
    private function distinctOptions(string $column): array
    {
        return DB::table('reporte_organico')
            ->whereNotNull($column)
            ->whereRaw("TRIM($column) <> ''")
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->toArray();
    }

    /** Opciones normalizadas de grado */
    private function buildGradoOptions(): array
    {
        $rows = DB::table('reporte_organico')
            ->whereNotNull('grado_organico')
            ->pluck('grado_organico');

        $tokens = [];
        foreach ($rows as $row) {
            $partes = preg_split('/[,;]+/u', (string) $row);
            foreach ($partes as $p) {
                $p = mb_strtoupper(trim($p));
                if ($p !== '') $tokens[] = $p;
            }
        }
        $tokens = array_values(array_unique($tokens));

        $orden = ['GRAS','GRAI','GRAD','CRNL','TCNL','MAYR','CPTN','TNTE','SBTE','SBOM','SBOP','SBOS','SGOP','SGOS','CBOP','CBOS','POLI'];
        usort($tokens, function ($a, $b) use ($orden) {
            $ia = array_search($a, $orden, true);
            $ib = array_search($b, $orden, true);
            $ia = ($ia === false) ? 999 : $ia;
            $ib = ($ib === false) ? 999 : $ib;
            return $ia <=> $ib;
        });
        return $tokens;
    }

    /** Opciones para el selector de subsistema */
    private function buildSubsistemaOptions(): array
    {
        return DB::table('reporte_organico')
            ->whereNotNull('subsistema')
            ->whereRaw("TRIM(subsistema) <> ''")
            ->select('subsistema')
            ->distinct()
            ->orderBy('subsistema')
            ->pluck('subsistema')
            ->toArray();
    }
}
