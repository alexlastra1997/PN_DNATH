<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteOrganicoExport;

class ReporteOrganicoVisualController extends Controller
{
    private bool $computeAlerts = true;

    /**
     * Normaliza texto para comparar:
     * - trim
     * - uppercase
     * - colapsa espacios dobles
     * - colapsa guiones repetidos
     * - quita guiones al final (MILAGRO- -> MILAGRO)
     */
    private function normNom(string $s): string
    {
        $s = mb_strtoupper(trim($s));
        $s = preg_replace('/\s+/u', ' ', $s);
        $s = preg_replace('/-+/u', '-', $s);
        $s = rtrim($s, '-');
        return trim($s);
    }

    private function normCargo(string $s): string
    {
        $s = mb_strtoupper(trim($s));
        $s = preg_replace('/\s+/u', ' ', $s);
        return trim($s);
    }

    public function index(Request $request)
    {
        DB::statement("SET SESSION group_concat_max_len = 1000000");

        $normArr = function ($arr) {
            $arr = is_array($arr) ? $arr : [];
            return array_values(array_filter(array_map(function ($v) {
                $v = mb_strtoupper(trim((string)$v));
                $v = preg_replace('/\s+/u', ' ', $v);
                return $v;
            }, $arr), fn($v) => $v !== ''));
        };

        $servicios     = $normArr($request->input('servicio', []));
        $nomenclaturas = $normArr($request->input('nomenclatura', [])); // filtros vienen ya "humanos"
        $cargos        = $normArr($request->input('cargo', []));
        $estados       = $normArr($request->input('estado', []));
        $subsistemas   = $normArr($request->input('subsistema', []));
        $grados        = $normArr($request->input('grado_organico', []));

        /**
         * IMPORTANTE:
         * Normalizamos en SQL la nomenclatura removiendo guiones finales para que:
         *   NDESC-...-MILAGRO-  ==  NDESC-...-MILAGRO
         */
        $RO_NOM_NORM = "TRIM(TRAILING '-' FROM UPPER(TRIM(ro.nomenclatura_organico)))";
        $RO_CARGO_NORM = "UPPER(TRIM(ro.cargo_organico))";

        $roQuery = DB::table('reporte_organico as ro')
            ->selectRaw('ro.servicio_organico')
            ->selectRaw('ro.nomenclatura_organico')
            ->selectRaw('ro.cargo_organico')
            ->selectRaw('ro.grado_organico')
            ->selectRaw('ro.numero_organico_ideal')
            ->selectRaw('ro.subsistema')
            ->selectRaw("$RO_NOM_NORM as ro_nom_norm")
            ->selectRaw("CONCAT(SUBSTRING_INDEX($RO_NOM_NORM,'-',5),'-') as ro_base_nom")
            ->selectRaw("$RO_CARGO_NORM as ro_cargo_norm")
            ->whereNotNull('ro.nomenclatura_organico')
            ->whereNotNull('ro.cargo_organico');

        if (!empty($servicios)) {
            $roQuery->where(function ($w) use ($servicios) {
                foreach ($servicios as $s) {
                    $w->orWhereRaw('UPPER(TRIM(ro.servicio_organico)) = ?', [$s]);
                }
            });
        }

        if (!empty($nomenclaturas)) {
            // OJO: aquí comparamos contra el campo normalizado (sin guion final)
            $roQuery->where(function ($w) use ($nomenclaturas) {
                foreach ($nomenclaturas as $n) {
                    $n = mb_strtoupper(trim($n));
                    $n = preg_replace('/\s+/u', ' ', $n);
                    $n = preg_replace('/-+/u', '-', $n);
                    $n = rtrim($n, '-');
                    $w->orWhereRaw("TRIM(TRAILING '-' FROM UPPER(TRIM(ro.nomenclatura_organico))) = ?", [$n]);
                }
            });
        }

        if (!empty($cargos)) {
            $roQuery->where(function ($w) use ($cargos) {
                foreach ($cargos as $c) {
                    $w->orWhereRaw('UPPER(TRIM(ro.cargo_organico)) = ?', [$c]);
                }
            });
        }

        if (!empty($subsistemas)) {
            $roQuery->where(function ($w) use ($subsistemas) {
                foreach ($subsistemas as $ss) {
                    $w->orWhereRaw('UPPER(TRIM(ro.subsistema)) = ?', [$ss]);
                }
            });
        }

        // ✅ filtro por grado_organico (CSV) usando REGEXP
        if (!empty($grados)) {
            $roQuery->where(function ($w) use ($grados) {
                foreach ($grados as $g) {
                    $pattern = '(^|[ ,;])' . preg_quote($g, '/') . '([ ,;]|$)';
                    $w->orWhereRaw("UPPER(ro.grado_organico) REGEXP ?", [$pattern]);
                }
            });
        }

        $roBase = DB::query()->fromSub($roQuery, 'rb')
            ->selectRaw('rb.servicio_organico')
            ->selectRaw('rb.nomenclatura_organico')
            ->selectRaw('rb.cargo_organico')
            ->selectRaw('MAX(rb.grado_organico) as grado_organico')
            ->selectRaw('MAX(rb.numero_organico_ideal) as numero_organico_ideal')
            ->selectRaw('MAX(rb.subsistema) as subsistema')
            ->selectRaw('MAX(rb.ro_nom_norm) as ro_nom_norm')
            ->selectRaw('MAX(rb.ro_base_nom) as ro_base_nom')
            ->selectRaw('MAX(rb.ro_cargo_norm) as ro_cargo_norm')
            ->groupBy('rb.servicio_organico', 'rb.nomenclatura_organico', 'rb.cargo_organico');

        // Usuarios normalizados (sin guion final)
        $U_NOM_NORM = "TRIM(TRAILING '-' FROM UPPER(TRIM(u.nomenclatura_efectiva)))";
        $U_CARGO_NORM = "UPPER(TRIM(u.funcion_efectiva))";

        $uQuery = DB::table('usuarios as u')
            ->selectRaw("$U_NOM_NORM as nom_norm")
            ->selectRaw("$U_CARGO_NORM as cargo_norm")
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('u.nomenclatura_efectiva')
            ->whereNotNull('u.funcion_efectiva')
            ->groupByRaw("$U_NOM_NORM, $U_CARGO_NORM");

        $uBaseQuery = DB::table('usuarios as u')
            ->selectRaw("CONCAT(SUBSTRING_INDEX($U_NOM_NORM,'-',5),'-') as base_nom")
            ->selectRaw("$U_CARGO_NORM as cargo_norm")
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('u.nomenclatura_efectiva')
            ->whereNotNull('u.funcion_efectiva')
            ->groupByRaw("CONCAT(SUBSTRING_INDEX($U_NOM_NORM,'-',5),'-'), $U_CARGO_NORM");

        $select = DB::query()->fromSub($roBase, 'rb')
            ->leftJoinSub($uQuery, 'ud', function ($j) {
                $j->on('ud.nom_norm', '=', 'rb.ro_nom_norm')
                    ->on('ud.cargo_norm', '=', 'rb.ro_cargo_norm');
            })
            ->leftJoinSub($uBaseQuery, 'ub', function ($j) {
                $j->on('ub.base_nom', '=', 'rb.ro_base_nom')
                    ->on('ub.cargo_norm', '=', 'rb.ro_cargo_norm');
            })
            ->selectRaw('rb.servicio_organico')
            ->selectRaw('rb.nomenclatura_organico')
            ->selectRaw('rb.cargo_organico')
            ->selectRaw('rb.numero_organico_ideal as organico_aprobado')
            ->selectRaw('COALESCE(ud.total, ub.total, 0) as organico_efectivo')
            ->selectRaw('rb.subsistema')
            ->selectRaw(
                $this->computeAlerts
                    ? "CASE WHEN TRIM(rb.grado_organico) = '' THEN NULL ELSE 1 END as tiene_alerta"
                    : "NULL as tiene_alerta"
            );

        // Filtro por estado
        $efectivoExpr = "COALESCE(ud.total, ub.total, 0)";
        if (!empty($estados)) {
            $select->where(function ($w) use ($estados, $efectivoExpr) {
                foreach ($estados as $e) {
                    if ($e === 'VACANTE') {
                        $w->orWhereRaw("$efectivoExpr < rb.numero_organico_ideal");
                    } elseif ($e === 'COMPLETO') {
                        $w->orWhereRaw("$efectivoExpr = rb.numero_organico_ideal");
                    } elseif ($e === 'EXCEDIDO') {
                        $w->orWhereRaw("$efectivoExpr > rb.numero_organico_ideal");
                    }
                }
            });
        }

        // ✅ Clonar antes de paginar
        $baseSelect = clone $select;

        $datos = (clone $baseSelect)
            ->orderBy('rb.servicio_organico')
            ->orderBy('rb.nomenclatura_organico')
            ->orderBy('rb.cargo_organico')
            ->paginate(100)
            ->appends($request->query());

        $totales = DB::query()->fromSub($baseSelect, 't')
            ->selectRaw('SUM(t.organico_aprobado) as total_aprobado')
            ->selectRaw('SUM(t.organico_efectivo) as total_efectivo')
            ->first();

        // ✅ Stats por subsistema (ya no referimos ud/ub/rb, solo t2.*)
        $statsSubsistema = DB::query()->fromSub($baseSelect, 't2')
            ->selectRaw('t2.subsistema')
            ->selectRaw("SUM(CASE WHEN t2.organico_efectivo < t2.organico_aprobado THEN 1 ELSE 0 END) as cargos_vacantes")
            ->selectRaw("SUM(CASE WHEN t2.organico_efectivo = t2.organico_aprobado THEN 1 ELSE 0 END) as cargos_completos")
            ->selectRaw("SUM(CASE WHEN t2.organico_efectivo > t2.organico_aprobado THEN 1 ELSE 0 END) as cargos_excedidos")
            ->groupBy('t2.subsistema')
            ->get();

        // Opciones filtros
        $opcionesServicio     = DB::table('reporte_organico')->whereNotNull('servicio_organico')->distinct()->orderBy('servicio_organico')->pluck('servicio_organico')->toArray();
        $opcionesNomenclatura = DB::table('reporte_organico')->whereNotNull('nomenclatura_organico')->distinct()->orderBy('nomenclatura_organico')->pluck('nomenclatura_organico')->toArray();
        $opcionesCargo        = DB::table('reporte_organico')->whereNotNull('cargo_organico')->distinct()->orderBy('cargo_organico')->pluck('cargo_organico')->toArray();
        $opcionesSubsistema   = DB::table('reporte_organico')->whereNotNull('subsistema')->distinct()->orderBy('subsistema')->pluck('subsistema')->toArray();

        // Opciones de grado (tokens desde CSV)
        $opcionesGradoOrganico = Cache::remember('ro_opciones_grado_organico', 60 * 60 * 6, function () {
            $rows = DB::table('reporte_organico')
                ->whereNotNull('grado_organico')
                ->pluck('grado_organico')
                ->toArray();

            $set = [];
            foreach ($rows as $csv) {
                $tokens = preg_split('/[,;]+/u', (string)$csv, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($tokens as $t) {
                    $t = mb_strtoupper(trim($t));
                    if ($t !== '') $set[$t] = true;
                }
            }

            $out = array_keys($set);
            sort($out);
            return $out;
        });

        return view('reporte_organico.visualizador', [
            'datos'                 => $datos,
            'totales'               => $totales,
            'statsSubsistema'       => $statsSubsistema,
            'opcionesServicio'      => $opcionesServicio,
            'opcionesNomenclatura'  => $opcionesNomenclatura,
            'opcionesCargo'         => $opcionesCargo,
            'opcionesSubsistema'    => $opcionesSubsistema,
            'opcionesGradoOrganico' => $opcionesGradoOrganico,
        ]);
    }

    public function ocupantes(Request $request)
    {
        // Normalizar parámetros entrantes (para arreglar MILAGRO- etc.)
        $nomenclatura = (string)$request->query('nomenclatura', '');
        $cargo        = (string)$request->query('cargo', '');

        $nomNorm   = $this->normNom($nomenclatura);
        $cargoNorm = $this->normCargo($cargo);

        // Para comparar con DB sin guion final:
        $U_NOM_NORM = "TRIM(TRAILING '-' FROM UPPER(TRIM(u.nomenclatura_efectiva)))";
        $U_CARGO_NORM = "UPPER(TRIM(u.funcion_efectiva))";

        $RO_NOM_NORM = "TRIM(TRAILING '-' FROM UPPER(TRIM(ro.nomenclatura_organico)))";
        $RO_CARGO_NORM = "UPPER(TRIM(ro.cargo_organico))";

        // Ocupantes exactos (normalizado)
        $ocupantesExact = DB::table('usuarios as u')
            ->select('u.cedula', 'u.grado', 'u.apellidos_nombres', 'u.estado_efectivo')
            ->whereRaw("$U_NOM_NORM = ?", [$nomNorm])
            ->whereRaw("$U_CARGO_NORM = ?", [$cargoNorm])
            ->orderBy('u.grado')
            ->orderBy('u.apellidos_nombres')
            ->get();

        // Info de orgánico exacta (normalizado)
        $infoCargo = DB::table('reporte_organico as ro')
            ->select(
                'ro.servicio_organico',
                'ro.nomenclatura_organico',
                'ro.cargo_organico',
                'ro.grado_organico',
                'ro.personal_organico',
                'ro.numero_organico_ideal',
                'ro.subsistema'
            )
            ->whereRaw("$RO_NOM_NORM = ?", [$nomNorm])
            ->whereRaw("$RO_CARGO_NORM = ?", [$cargoNorm])
            ->orderBy('ro.servicio_organico')
            ->get();

        // Helper grados permitidos desde infoCargo
        $gradosPermitidos = [];
        foreach ($infoCargo as $fila) {
            $tokens = preg_split('/[,;]+/u', (string)($fila->grado_organico ?? ''), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($tokens as $t) {
                $t = mb_strtoupper(trim($t));
                if ($t !== '') $gradosPermitidos[$t] = true;
            }
        }
        $gradosPermitidos = array_keys($gradosPermitidos);
        sort($gradosPermitidos);

        // Si hay exactos, retornamos
        if ($ocupantesExact->count() > 0) {
            return view('reporte_organico.ocupantes', [
                'ocupantes'        => $ocupantesExact,
                'infoCargo'        => $infoCargo,
                'nomenclatura'     => $nomenclatura, // mostramos el original
                'cargo'            => $cargo,
                'gradosPermitidos' => $gradosPermitidos,
            ]);
        }

        // Fallback por base 5 tokens (ya con nomNorm sin guion final)
        $baseNom = implode('-', array_slice(explode('-', $nomNorm), 0, 5));

        $ocupantesBase = DB::table('usuarios as u')
            ->select('u.cedula', 'u.grado', 'u.apellidos_nombres', 'u.estado_efectivo', 'u.nomenclatura_efectiva', 'u.funcion_efectiva')
            ->whereRaw("CONCAT(SUBSTRING_INDEX($U_NOM_NORM,'-',5),'-') = ?", [$baseNom . '-'])
            ->whereRaw("$U_CARGO_NORM = ?", [$cargoNorm])
            ->orderBy('u.grado')
            ->orderBy('u.apellidos_nombres')
            ->get();

        $infoCargoBase = DB::table('reporte_organico as ro')
            ->select(
                'ro.servicio_organico',
                'ro.nomenclatura_organico',
                'ro.cargo_organico',
                'ro.grado_organico',
                'ro.personal_organico',
                'ro.numero_organico_ideal',
                'ro.subsistema'
            )
            ->whereRaw("CONCAT(SUBSTRING_INDEX($RO_NOM_NORM,'-',5),'-') = ?", [$baseNom . '-'])
            ->whereRaw("$RO_CARGO_NORM = ?", [$cargoNorm])
            ->orderBy('ro.servicio_organico')
            ->get();

        // grados permitidos (base)
        $gradosPermitidos = [];
        foreach ($infoCargoBase as $fila) {
            $tokens = preg_split('/[,;]+/u', (string)($fila->grado_organico ?? ''), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($tokens as $t) {
                $t = mb_strtoupper(trim($t));
                if ($t !== '') $gradosPermitidos[$t] = true;
            }
        }
        $gradosPermitidos = array_keys($gradosPermitidos);
        sort($gradosPermitidos);

        return view('reporte_organico.ocupantes', [
            'ocupantes'        => $ocupantesBase,
            'infoCargo'        => $infoCargoBase,
            'nomenclatura'     => $nomenclatura,
            'cargo'            => $cargo,
            'gradosPermitidos' => $gradosPermitidos,
            'fallbackBaseNom'  => $baseNom,
        ]);
    }

    public function exportarExcel(Request $request)
    {
        DB::statement("SET SESSION group_concat_max_len = 1000000");

        $normArr = function ($arr) {
            $arr = is_array($arr) ? $arr : [];
            return array_values(array_filter(array_map(function ($v) {
                $v = mb_strtoupper(trim((string)$v));
                $v = preg_replace('/\s+/u', ' ', $v);
                return $v;
            }, $arr), fn($v) => $v !== ''));
        };

        $servicios     = $normArr($request->input('servicio', []));
        $nomenclaturas = $normArr($request->input('nomenclatura', []));
        $cargos        = $normArr($request->input('cargo', []));
        $estados       = $normArr($request->input('estado', []));
        $subsistemas   = $normArr($request->input('subsistema', []));
        $grados        = $normArr($request->input('grado_organico', []));

        $RO_NOM_NORM = "TRIM(TRAILING '-' FROM UPPER(TRIM(ro.nomenclatura_organico)))";
        $RO_CARGO_NORM = "UPPER(TRIM(ro.cargo_organico))";

        $roQuery = DB::table('reporte_organico as ro')
            ->selectRaw('ro.servicio_organico')
            ->selectRaw('ro.nomenclatura_organico')
            ->selectRaw('ro.cargo_organico')
            ->selectRaw('ro.grado_organico')
            ->selectRaw('ro.numero_organico_ideal')
            ->selectRaw('ro.subsistema')
            ->selectRaw("$RO_NOM_NORM as ro_nom_norm")
            ->selectRaw("CONCAT(SUBSTRING_INDEX($RO_NOM_NORM,'-',5),'-') as ro_base_nom")
            ->selectRaw("$RO_CARGO_NORM as ro_cargo_norm")
            ->whereNotNull('ro.nomenclatura_organico')
            ->whereNotNull('ro.cargo_organico');

        if (!empty($servicios)) {
            $roQuery->where(function ($w) use ($servicios) {
                foreach ($servicios as $s) {
                    $w->orWhereRaw('UPPER(TRIM(ro.servicio_organico)) = ?', [$s]);
                }
            });
        }
        if (!empty($nomenclaturas)) {
            $roQuery->where(function ($w) use ($nomenclaturas) {
                foreach ($nomenclaturas as $n) {
                    $n = mb_strtoupper(trim($n));
                    $n = preg_replace('/\s+/u', ' ', $n);
                    $n = preg_replace('/-+/u', '-', $n);
                    $n = rtrim($n, '-');
                    $w->orWhereRaw("TRIM(TRAILING '-' FROM UPPER(TRIM(ro.nomenclatura_organico))) = ?", [$n]);
                }
            });
        }
        if (!empty($cargos)) {
            $roQuery->where(function ($w) use ($cargos) {
                foreach ($cargos as $c) {
                    $w->orWhereRaw('UPPER(TRIM(ro.cargo_organico)) = ?', [$c]);
                }
            });
        }
        if (!empty($subsistemas)) {
            $roQuery->where(function ($w) use ($subsistemas) {
                foreach ($subsistemas as $ss) {
                    $w->orWhereRaw('UPPER(TRIM(ro.subsistema)) = ?', [$ss]);
                }
            });
        }

        // ✅ filtro por grado en export
        if (!empty($grados)) {
            $roQuery->where(function ($w) use ($grados) {
                foreach ($grados as $g) {
                    $pattern = '(^|[ ,;])' . preg_quote($g, '/') . '([ ,;]|$)';
                    $w->orWhereRaw("UPPER(ro.grado_organico) REGEXP ?", [$pattern]);
                }
            });
        }

        $roBase = DB::query()->fromSub($roQuery, 'rb')
            ->selectRaw('rb.servicio_organico')
            ->selectRaw('rb.nomenclatura_organico')
            ->selectRaw('rb.cargo_organico')
            ->selectRaw('MAX(rb.grado_organico) as grado_organico')
            ->selectRaw('MAX(rb.numero_organico_ideal) as numero_organico_ideal')
            ->selectRaw('MAX(rb.subsistema) as subsistema')
            ->selectRaw('MAX(rb.ro_nom_norm) as ro_nom_norm')
            ->selectRaw('MAX(rb.ro_base_nom) as ro_base_nom')
            ->selectRaw('MAX(rb.ro_cargo_norm) as ro_cargo_norm')
            ->groupBy('rb.servicio_organico', 'rb.nomenclatura_organico', 'rb.cargo_organico');

        $U_NOM_NORM = "TRIM(TRAILING '-' FROM UPPER(TRIM(u.nomenclatura_efectiva)))";
        $U_CARGO_NORM = "UPPER(TRIM(u.funcion_efectiva))";

        $uQuery = DB::table('usuarios as u')
            ->selectRaw("$U_NOM_NORM as nom_norm")
            ->selectRaw("$U_CARGO_NORM as cargo_norm")
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('u.nomenclatura_efectiva')
            ->whereNotNull('u.funcion_efectiva')
            ->groupByRaw("$U_NOM_NORM, $U_CARGO_NORM");

        $uBaseQuery = DB::table('usuarios as u')
            ->selectRaw("CONCAT(SUBSTRING_INDEX($U_NOM_NORM,'-',5),'-') as base_nom")
            ->selectRaw("$U_CARGO_NORM as cargo_norm")
            ->selectRaw('COUNT(*) as total')
            ->whereNotNull('u.nomenclatura_efectiva')
            ->whereNotNull('u.funcion_efectiva')
            ->groupByRaw("CONCAT(SUBSTRING_INDEX($U_NOM_NORM,'-',5),'-'), $U_CARGO_NORM");

        $select = DB::query()->fromSub($roBase, 'rb')
            ->leftJoinSub($uQuery, 'ud', function ($j) {
                $j->on('ud.nom_norm', '=', 'rb.ro_nom_norm')
                    ->on('ud.cargo_norm', '=', 'rb.ro_cargo_norm');
            })
            ->leftJoinSub($uBaseQuery, 'ub', function ($j) {
                $j->on('ub.base_nom', '=', 'rb.ro_base_nom')
                    ->on('ub.cargo_norm', '=', 'rb.ro_cargo_norm');
            })
            ->selectRaw('rb.servicio_organico')
            ->selectRaw('rb.nomenclatura_organico')
            ->selectRaw('rb.cargo_organico')
            ->selectRaw('rb.grado_organico')
            ->selectRaw('rb.numero_organico_ideal as organico_aprobado')
            ->selectRaw('COALESCE(ud.total, ub.total, 0) as organico_efectivo')
            ->selectRaw('rb.subsistema');

        $efectivoExpr = "COALESCE(ud.total, ub.total, 0)";
        if (!empty($estados)) {
            $select->where(function ($w) use ($estados, $efectivoExpr) {
                foreach ($estados as $e) {
                    if ($e === 'VACANTE') {
                        $w->orWhereRaw("$efectivoExpr < rb.numero_organico_ideal");
                    } elseif ($e === 'COMPLETO') {
                        $w->orWhereRaw("$efectivoExpr = rb.numero_organico_ideal");
                    } elseif ($e === 'EXCEDIDO') {
                        $w->orWhereRaw("$efectivoExpr > rb.numero_organico_ideal");
                    }
                }
            });
        }

        $rows = $select
            ->orderBy('rb.servicio_organico')
            ->orderBy('rb.nomenclatura_organico')
            ->orderBy('rb.cargo_organico')
            ->get();

        return Excel::download(new ReporteOrganicoExport($rows), 'reporte_organico_filtrado.xlsx');
    }
}
