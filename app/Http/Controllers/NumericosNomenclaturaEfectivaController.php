<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class NumericosNomenclaturaEfectivaController extends Controller
{
    /**
     * Vista de numéricos:
     * - NDESC (zonas, subzonas, distritos, consolidado por cuadro_policial).
     * - Tránsito (NDESC-Z4-SZ-STO DOMINGO TSACHILAS-JPREV-CTSV-...).
     * - Cárceles (CCP/CPL).
     * - Unidades (resto de nomenclaturas, agrupadas por tronco).
     * Todo filtrable por grados desde la columna grado.
     */
    public function index(Request $request)
    {
        $table     = 'usuarios';
        $col       = 'nomenclatura_efectiva';
        $gradoCol  = 'grado';
        $estadoCol = 'estado_efectivo';

        // Normalización de nomenclatura efectiva (MySQL 8+)
        $norm = "UPPER(REGEXP_REPLACE(TRIM($col), '-+', '-'))";

        // Tokens NDESC
        $zonaExpr     = "REGEXP_SUBSTR($norm, 'NDESC-Z[1-9]')";
        $hasSzExpr    = "$norm LIKE '%-SZ-%'";
        $subzonaExpr  = "CASE WHEN $hasSzExpr THEN REGEXP_SUBSTR($norm, 'SZ-[^-]+') END";
        $hasDExpr     = "$norm LIKE '%-D-%'";
        $distritoRaw  = "CASE WHEN $hasDExpr THEN REGEXP_SUBSTR($norm, 'D-[^-]+') END";
        $distritoExpr = "NULLIF($distritoRaw, 'D-DP')";
        $onlyNdescWhere = "$norm LIKE 'NDESC-%'";

        // === Filtro por grados ===
        $gradosSeleccionados = array_filter(
            (array) $request->input('grados', []),
            fn($g) => $g !== null && $g !== ''
        );

        // Distintos grados disponibles
        $gradosDisponibles = DB::table($table)
            ->select($gradoCol)
            ->whereNotNull($gradoCol)
            ->where($gradoCol, '<>', '')
            ->groupBy($gradoCol)
            ->orderBy($gradoCol)
            ->pluck($gradoCol)
            ->toArray();

        // Helper para aplicar filtro de grado
        $applyGradoFilter = function ($query) use ($gradosSeleccionados, $gradoCol) {
            if (!empty($gradosSeleccionados)) {
                $query->whereIn($gradoCol, $gradosSeleccionados);
            }
            return $query;
        };

        // Helper para normalizar cadenas en PHP (para unir organico/usuarios)
        $phpNorm = fn($s) => strtoupper(preg_replace('/-+/', '-', trim((string) $s)));

        // ========== NDESC ==========

        $totalFilas = $applyGradoFilter(
            DB::table($table)->whereRaw($onlyNdescWhere)
        )->count();

        // Zonas (todas)
        $porZonas = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$zonaExpr AS zona, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
        )
            ->groupBy(DB::raw($zonaExpr))
            ->orderBy('zona')
            ->get();

        // Zonas (solo sin SZ-)
        $porZonasSoloSinSZ = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$zonaExpr AS zona, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
                ->whereRaw("($hasSzExpr) = 0")
        )
            ->groupBy(DB::raw($zonaExpr))
            ->orderBy('zona')
            ->get();

        $totalSinSZ = $applyGradoFilter(
            DB::table($table)
                ->whereRaw($onlyNdescWhere)
                ->whereRaw("($hasSzExpr) = 0")
        )->count();

        // Subzonas (con SZ-)
        $porSubzonas = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$subzonaExpr AS subzona, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
                ->whereRaw("($hasSzExpr) = 1")
        )
            ->groupBy(DB::raw($subzonaExpr))
            ->orderBy('subzona')
            ->get();

        // Distritos (con D- válido)
        $porDistritos = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$distritoExpr AS distrito, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
                ->whereRaw("($hasDExpr) = 1")
                ->whereRaw("$distritoExpr IS NOT NULL AND $distritoExpr <> ''")
        )
            ->groupBy(DB::raw($distritoExpr))
            ->orderBy('distrito')
            ->get();

        // Consolidado NDESC
        $consolidado = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$zonaExpr AS zona, $subzonaExpr AS subzona, $distritoExpr AS distrito, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
        )
            ->groupBy(DB::raw($zonaExpr))
            ->groupBy(DB::raw($subzonaExpr))
            ->groupBy(DB::raw($distritoExpr))
            ->orderBy('zona')
            ->orderBy('subzona')
            ->orderBy('distrito')
            ->get();

        // Pivot por cuadro_policial
        $porGrupoYCuadro = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$zonaExpr AS zona, $subzonaExpr AS subzona, $distritoExpr AS distrito, UPPER(TRIM(cuadro_policial)) AS cp, COUNT(*) AS cnt")
                ->whereRaw($onlyNdescWhere)
        )
            ->groupBy(DB::raw($zonaExpr))
            ->groupBy(DB::raw($subzonaExpr))
            ->groupBy(DB::raw($distritoExpr))
            ->groupBy(DB::raw('UPPER(TRIM(cuadro_policial))'))
            ->get();

        $mapCp = [];
        foreach ($porGrupoYCuadro as $r) {
            $key = ($r->zona ?? '') . '|' . ($r->subzona ?? '') . '|' . ($r->distrito ?? '');
            if (!isset($mapCp[$key])) $mapCp[$key] = [];
            $mapCp[$key][$r->cp ?: ''] = (int) $r->cnt;
        }

        $normCp = function ($cp) {
            $s = strtoupper(trim((string)$cp));
            return preg_replace('/\s+/', ' ', $s);
        };
        $isSup = function ($cp) use ($normCp) {
            $s = $normCp($cp);
            return (str_contains($s, 'OFICIA') && str_contains($s, 'SUPERIO')) || str_contains($s, 'OF SUP');
        };
        $isSub = function ($cp) use ($normCp) {
            $s = $normCp($cp);
            return str_contains($s, 'SUBALTER') || str_contains($s, 'OF SUB');
        };
        $isClasesPol = function ($cp) use ($normCp) {
            $s = $normCp($cp);
            return str_contains($s, 'CLASE') || str_contains($s, 'POLICI') || str_contains($s, 'TROPA');
        };

        $consolidadoCp = $consolidado->map(function ($row) use ($mapCp, $isSup, $isSub, $isClasesPol) {
            $key  = ($row->zona ?? '') . '|' . ($row->subzona ?? '') . '|' . ($row->distrito ?? '');
            $buck = $mapCp[$key] ?? [];

            $sup = 0; $sub = 0; $clp = 0;
            foreach ($buck as $cp => $cnt) {
                if     ($isSup($cp))       $sup += $cnt;
                elseif ($isSub($cp))       $sub += $cnt;
                elseif ($isClasesPol($cp)) $clp += $cnt;
            }

            $row->sup   = $sup;
            $row->sub   = $sub;
            $row->clpol = $clp;
            return $row;
        });

        // KPIs NDESC
        $zonasUnicas     = $porZonas->filter(fn($z) => !empty($z->zona))->count();
        $subzonasUnicas  = $porSubzonas->filter(fn($s) => !empty($s->subzona))->count();
        $distritosUnicos = $porDistritos->filter(fn($d) => !empty($d->distrito))->count();

        // Estados efectivos NDESC
        $ndescEstados = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$estadoCol AS estado_efectivo, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
        )
            ->groupBy($estadoCol)
            ->get();

        // ========== TRÁNSITO (NDESC-Z4-SZ-STO DOMINGO TSACHILAS-JPREV-CTSV-) ==========

        $transitoWhere = "$norm LIKE 'NDESC-Z4-SZ-STO DOMINGO TSACHILAS-JPREV-CTSV-%'";

        $transitoTabla = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$zonaExpr AS zona, $subzonaExpr AS subzona, $distritoExpr AS distrito, COUNT(*) AS total")
                ->whereRaw($onlyNdescWhere)
                ->whereRaw($transitoWhere)
        )
            ->groupBy(DB::raw($zonaExpr))
            ->groupBy(DB::raw($subzonaExpr))
            ->groupBy(DB::raw($distritoExpr))
            ->orderBy('zona')
            ->orderBy('subzona')
            ->orderBy('distrito')
            ->get();

        $transitoEstados = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$estadoCol AS estado_efectivo, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
                ->whereRaw($transitoWhere)
        )
            ->groupBy($estadoCol)
            ->get();

        // ========== CÁRCELES (CCP / CPL) ==========

        $carcelesLikeRaw = "$norm LIKE '%CCP%' OR $norm LIKE '%CPL%'";

        // Detalle por zona / subzona / distrito / nomenclatura / grado
        $carcelesDetQuery = DB::table($table)
            ->selectRaw("
                $zonaExpr     AS zona,
                $subzonaExpr  AS subzona,
                $distritoExpr AS distrito,
                $col          AS nomenclatura,
                $gradoCol     AS grado,
                COUNT(*)      AS cantidad
            ")
            ->whereRaw($onlyNdescWhere)
            ->where(function ($q) use ($carcelesLikeRaw) {
                $q->whereRaw($carcelesLikeRaw);
            });

        $carcelesDetQuery = $applyGradoFilter($carcelesDetQuery);
        $carcelesDet      = $carcelesDetQuery
            ->groupBy(DB::raw($zonaExpr))
            ->groupBy(DB::raw($subzonaExpr))
            ->groupBy(DB::raw($distritoExpr))
            ->groupBy($col)
            ->groupBy($gradoCol)
            ->orderBy($col)
            ->get();

        // Pivot a filas: una fila por nomenclatura, columnas por grado
        $carcelesMap = [];
        foreach ($carcelesDet as $r) {
            $key = ($r->zona ?? '') . '|' . ($r->subzona ?? '') . '|' . ($r->distrito ?? '') . '|' . $r->nomenclatura;
            if (!isset($carcelesMap[$key])) {
                $carcelesMap[$key] = [
                    'zona'         => $r->zona,
                    'subzona'      => $r->subzona,
                    'distrito'     => $r->distrito,
                    'nomenclatura' => $r->nomenclatura,
                    'total'        => 0,
                    'por_grado'    => [],
                ];
            }
            $carcelesMap[$key]['total'] += (int) $r->cantidad;
            $g = $r->grado ?? '(SIN GRADO)';
            if (!isset($carcelesMap[$key]['por_grado'][$g])) {
                $carcelesMap[$key]['por_grado'][$g] = 0;
            }
            $carcelesMap[$key]['por_grado'][$g] += (int) $r->cantidad;
        }

        $carcelesTabla = array_values($carcelesMap);

        // Estados efectivos Cárceles
        $carcelesEstados = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$estadoCol AS estado_efectivo, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
                ->where(function ($q) use ($carcelesLikeRaw) {
                    $q->whereRaw($carcelesLikeRaw);
                })
        )
            ->groupBy($estadoCol)
            ->get();

        // ========== UNIDADES (resto de nomenclaturas) ==========

        // Tronco especial:
        // - Para NAP-DNAIS-..., NAP-DNED-... y NOPERA-... se toma hasta el 3er nivel.
        // - Para el resto, hasta el 2do nivel.
        $troncoExpr = "
            CASE
                WHEN $norm LIKE 'NAP-DNAIS-%'
                  OR $norm LIKE 'NAP-DNED-%'
                  OR $norm LIKE 'NOPERA-%'
                THEN SUBSTRING_INDEX($norm, '-', 3)
                ELSE SUBSTRING_INDEX($norm, '-', 2)
            END
        ";

        $normOrg   = "UPPER(REGEXP_REPLACE(TRIM(nomenclatura_organico), '-+', '-'))";
        $troncoOrg = "
            CASE
                WHEN $normOrg LIKE 'NAP-DNAIS-%'
                  OR $normOrg LIKE 'NAP-DNED-%'
                  OR $normOrg LIKE 'NOPERA-%'
                THEN SUBSTRING_INDEX($normOrg, '-', 3)
                ELSE SUBSTRING_INDEX($normOrg, '-', 2)
            END
        ";

        // Base usuarios (resumen por tronco)
        $otrasBase = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$troncoExpr AS tronco, COUNT(*) AS seleccionados")
                ->whereRaw("$norm NOT LIKE 'NDESC-%'")
                ->whereRaw("$norm NOT LIKE '%CCP%'")
                ->whereRaw("$norm NOT LIKE '%CPL%'")
        )
            ->groupBy(DB::raw($troncoExpr))
            ->orderBy('tronco')
            ->get();

        // Base organico (por tronco y grados seleccionados)
        $orgOtrasQuery = DB::table('reporte_organico')
            ->selectRaw("$troncoOrg AS tronco, SUM(numero_organico_ideal) AS aprobado")
            ->whereRaw("$normOrg NOT LIKE 'NDESC-%'")
            ->whereRaw("$normOrg NOT LIKE '%CCP%'")
            ->whereRaw("$normOrg NOT LIKE '%CPL%'");

        if (!empty($gradosSeleccionados)) {
            $orgOtrasQuery->whereIn(DB::raw('UPPER(TRIM(grado_organico))'), array_map('strtoupper', $gradosSeleccionados));
        }

        $orgOtras = $orgOtrasQuery
            ->groupBy(DB::raw($troncoOrg))
            ->get();

        $orgOtrasMap = [];
        foreach ($orgOtras as $o) {
            $orgOtrasMap[$o->tronco] = (int) $o->aprobado;
        }

        // Resumen unidades: tronco + aprobado/efectivo/diferencia
        $otrasTabla = $otrasBase->map(function ($row) use ($orgOtrasMap) {
            $tronco          = $row->tronco;
            $efectivo        = (int) $row->seleccionados;
            $aprobado        = (int) ($orgOtrasMap[$tronco] ?? 0);
            $row->aprobado   = $aprobado;
            $row->diferencia = $aprobado - $efectivo;
            return $row;
        });

        // Detalle para modales de UNIDADES
        // Usuarios: tronco + nomenclatura + estado_efectivo
        $otrasUsuariosDet = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$troncoExpr AS tronco, $col AS nomenclatura, $estadoCol AS estado_efectivo, COUNT(*) AS cnt")
                ->whereRaw("$norm NOT LIKE 'NDESC-%'")
                ->whereRaw("$norm NOT LIKE '%CCP%'")
                ->whereRaw("$norm NOT LIKE '%CPL%'")
        )
            ->groupBy(DB::raw($troncoExpr))
            ->groupBy($col)
            ->groupBy($estadoCol)
            ->orderBy($col)
            ->get();

        // Organico: tronco + nomenclatura_organico
        $orgDetalleQuery = DB::table('reporte_organico')
            ->selectRaw("$troncoOrg AS tronco, nomenclatura_organico, SUM(numero_organico_ideal) AS aprobado")
            ->whereRaw("$normOrg NOT LIKE 'NDESC-%'")
            ->whereRaw("$normOrg NOT LIKE '%CCP%'")
            ->whereRaw("$normOrg NOT LIKE '%CPL%'");

        if (!empty($gradosSeleccionados)) {
            $orgDetalleQuery->whereIn(DB::raw('UPPER(TRIM(grado_organico))'), array_map('strtoupper', $gradosSeleccionados));
        }

        $orgDetalle = $orgDetalleQuery
            ->groupBy(DB::raw($troncoOrg))
            ->groupBy('nomenclatura_organico')
            ->get();

        $orgDetalleMap = [];
        foreach ($orgDetalle as $o) {
            $t   = $o->tronco;
            $key = $phpNorm($o->nomenclatura_organico);
            if (!isset($orgDetalleMap[$t])) {
                $orgDetalleMap[$t] = [];
            }
            $orgDetalleMap[$t][$key] = (int) $o->aprobado;
        }

        $otrasDetalle = [];

        foreach ($otrasUsuariosDet as $r) {
            $tronco    = $r->tronco ?? '(SIN TRONCO)';
            $nom       = $r->nomenclatura;
            $nomKey    = $phpNorm($nom);
            $estadoVal = strtoupper(trim((string) $r->estado_efectivo));
            $cnt       = (int) $r->cnt;

            if (!isset($otrasDetalle[$tronco])) {
                $otrasDetalle[$tronco] = [];
            }
            if (!isset($otrasDetalle[$tronco][$nomKey])) {
                $aprobado = $orgDetalleMap[$tronco][$nomKey] ?? 0;
                $otrasDetalle[$tronco][$nomKey] = (object) [
                    'nomenclatura'       => $nom,
                    'aprobado'           => (int) $aprobado,
                    'efectivo'           => 0,
                    'diferencia'         => 0,
                    'unidad_origen'      => 0,
                    'traslado_temporal'  => 0,
                    'traslado_excedente' => 0,
                    'traslado_eventual'  => 0,
                    'otros'              => 0,
                ];
            }

            $obj = $otrasDetalle[$tronco][$nomKey];

            $obj->efectivo += $cnt;

            switch ($estadoVal) {
                case 'UNIDAD DE ORIGEN':
                    $obj->unidad_origen += $cnt;
                    break;
                case 'TRASLADO TEMPORAL':
                    $obj->traslado_temporal += $cnt;
                    break;
                case 'TRASLADO TEMPORAL POR EXCEDENTE':
                    $obj->traslado_excedente += $cnt;
                    break;
                case 'TRASLADO EVENTUAL':
                    $obj->traslado_eventual += $cnt;
                    break;
                default:
                    $obj->otros += $cnt;
                    break;
            }
        }

        // Calcular diferencia para cada nomenclatura en detalle
        foreach ($otrasDetalle as $tronco => $lista) {
            foreach ($lista as $key => $obj) {
                $obj->diferencia = (int) $obj->aprobado - (int) $obj->efectivo;
            }
            // Convertir a lista indexada
            $otrasDetalle[$tronco] = array_values($lista);
        }

        // Estados efectivos UNIDADES (resto)
        $otrasEstados = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$estadoCol AS estado_efectivo, COUNT(*) AS cantidad")
                ->whereRaw("$norm NOT LIKE 'NDESC-%'")
                ->whereRaw("$norm NOT LIKE '%CCP%'")
                ->whereRaw("$norm NOT LIKE '%CPL%'")
        )
            ->groupBy($estadoCol)
            ->get();

        // ============================
        // Retornar vista
        // ============================
        return view('numericos.nomenclatura_efectiva', [
            'soloNdesc'          => true,
            'gradosSeleccionados'=> $gradosSeleccionados,
            'gradosDisponibles'  => $gradosDisponibles,

            // NDESC
            'totalFilas'     => $totalFilas,
            'porZonas'       => $porZonas,
            'porZonasSoloSinSZ' => $porZonasSoloSinSZ,
            'totalSinSZ'     => $totalSinSZ,
            'porSubzonas'    => $porSubzonas,
            'porDistritos'   => $porDistritos,
            'consolidadoCp'  => $consolidadoCp,
            'zonasUnicas'    => $zonasUnicas,
            'subzonasUnicas' => $subzonasUnicas,
            'distritosUnicos'=> $distritosUnicos,
            'ndescEstados'   => $ndescEstados,

            // Tránsito
            'transitoTabla'   => $transitoTabla,
            'transitoEstados' => $transitoEstados,

            // Cárceles
            'carcelesTabla'   => $carcelesTabla,
            'carcelesEstados' => $carcelesEstados,

            // Unidades (resto)
            'otrasTabla'    => $otrasTabla,
            'otrasDetalle'  => $otrasDetalle,
            'otrasEstados'  => $otrasEstados,
        ]);
    }

    /**
     * Exporta a Excel:
     * 01 Zonas, 02 Zonas sin SZ, 03 Subzonas, 04 Distritos,
     * 05 Consolidado, 06 Cárceles, 07 Unidades.
     * Respeta el filtro por grados.
     */
    public function export(Request $request)
    {
        $table     = 'usuarios';
        $col       = 'nomenclatura_efectiva';
        $gradoCol  = 'grado';
        $estadoCol = 'estado_efectivo';

        $norm = "UPPER(REGEXP_REPLACE(TRIM($col), '-+', '-'))";

        $zonaExpr     = "REGEXP_SUBSTR($norm, 'NDESC-Z[1-9]')";
        $hasSzExpr    = "$norm LIKE '%-SZ-%'";
        $subzonaExpr  = "CASE WHEN $hasSzExpr THEN REGEXP_SUBSTR($norm, 'SZ-[^-]+') END";
        $hasDExpr     = "$norm LIKE '%-D-%'";
        $distritoRaw  = "CASE WHEN $hasDExpr THEN REGEXP_SUBSTR($norm, 'D-[^-]+') END";
        $distritoExpr = "NULLIF($distritoRaw, 'D-DP')";
        $onlyNdescWhere = "$norm LIKE 'NDESC-%'";

        $gradosSeleccionados = array_filter(
            (array) $request->input('grados', []),
            fn($g) => $g !== null && $g !== ''
        );

        $applyGradoFilter = function ($query) use ($gradosSeleccionados, $gradoCol) {
            if (!empty($gradosSeleccionados)) {
                $query->whereIn($gradoCol, $gradosSeleccionados);
            }
            return $query;
        };

        $phpNorm = fn($s) => strtoupper(preg_replace('/-+/', '-', trim((string) $s)));

        // ===== NDESC para Excel =====

        $zonas = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$zonaExpr AS zona, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
        )
            ->groupBy(DB::raw($zonaExpr))
            ->orderBy('zona')
            ->get()
            ->map(fn($r) => ['Zona' => $r->zona, 'Cantidad' => (int)$r->cantidad])
            ->values()->all();

        $zonasSinSz = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$zonaExpr AS zona, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
                ->whereRaw("($hasSzExpr) = 0")
        )
            ->groupBy(DB::raw($zonaExpr))
            ->orderBy('zona')
            ->get()
            ->map(fn($r) => ['Zona' => $r->zona, 'Cantidad' => (int)$r->cantidad])
            ->values()->all();

        $subzonas = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$subzonaExpr AS subzona, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
                ->whereRaw("($hasSzExpr) = 1")
        )
            ->groupBy(DB::raw($subzonaExpr))
            ->orderBy('subzona')
            ->get()
            ->map(fn($r) => ['Subzona' => $r->subzona, 'Cantidad' => (int)$r->cantidad])
            ->values()->all();

        $distritos = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$distritoExpr AS distrito, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
                ->whereRaw("($hasDExpr) = 1")
                ->whereRaw("$distritoExpr IS NOT NULL AND $distritoExpr <> ''")
        )
            ->groupBy(DB::raw($distritoExpr))
            ->orderBy('distrito')
            ->get()
            ->map(fn($r) => ['Distrito' => $r->distrito, 'Cantidad' => (int)$r->cantidad])
            ->values()->all();

        $consolidado = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$zonaExpr AS zona, $subzonaExpr AS subzona, $distritoExpr AS distrito, COUNT(*) AS cantidad")
                ->whereRaw($onlyNdescWhere)
        )
            ->groupBy(DB::raw($zonaExpr))
            ->groupBy(DB::raw($subzonaExpr))
            ->groupBy(DB::raw($distritoExpr))
            ->orderBy('zona')
            ->orderBy('subzona')
            ->orderBy('distrito')
            ->get();

        // Conteos por cuadro_policial
        $porGrupoYCuadro = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$zonaExpr AS zona, $subzonaExpr AS subzona, $distritoExpr AS distrito, UPPER(TRIM(cuadro_policial)) AS cp, COUNT(*) AS cnt")
                ->whereRaw($onlyNdescWhere)
        )
            ->groupBy(DB::raw($zonaExpr))
            ->groupBy(DB::raw($subzonaExpr))
            ->groupBy(DB::raw($distritoExpr))
            ->groupBy(DB::raw('UPPER(TRIM(cuadro_policial))'))
            ->get();

        $mapCp = [];
        foreach ($porGrupoYCuadro as $r) {
            $key = ($r->zona ?? '') . '|' . ($r->subzona ?? '') . '|' . ($r->distrito ?? '');
            if (!isset($mapCp[$key])) $mapCp[$key] = [];
            $mapCp[$key][$r->cp ?: ''] = (int) $r->cnt;
        }

        $normCp = function ($cp) {
            $s = strtoupper(trim((string)$cp));
            return preg_replace('/\s+/', ' ', $s);
        };
        $isSup = function ($cp) use ($normCp) {
            $s = $normCp($cp);
            return (str_contains($s, 'OFICIA') && str_contains($s, 'SUPERIO')) || str_contains($s, 'OF SUP');
        };
        $isSub = function ($cp) use ($normCp) {
            $s = $normCp($cp);
            return str_contains($s, 'SUBALTER') || str_contains($s, 'OF SUB');
        };
        $isClasesPol = function ($cp) use ($normCp) {
            $s = $normCp($cp);
            return str_contains($s, 'CLASE') || str_contains($s, 'POLICI') || str_contains($s, 'TROPA');
        };

        $consolidadoCp = $consolidado->map(function ($row) use ($mapCp, $isSup, $isSub, $isClasesPol) {
            $key  = ($row->zona ?? '') . '|' . ($row->subzona ?? '') . '|' . ($row->distrito ?? '');
            $buck = $mapCp[$key] ?? [];

            $sup = 0; $sub = 0; $clp = 0;
            foreach ($buck as $cp => $cnt) {
                if     ($isSup($cp))       $sup += $cnt;
                elseif ($isSub($cp))       $sub += $cnt;
                elseif ($isClasesPol($cp)) $clp += $cnt;
            }

            return [
                'Zona'              => $row->zona ?? '',
                'Subzona'           => $row->subzona ?? '',
                'Distrito'          => $row->distrito ?? '',
                'Total'             => (int) $row->cantidad,
                'Of. Superiores'    => $sup,
                'Of. Subalternos'   => $sub,
                'Clases y Policías' => $clp,
            ];
        })->values()->all();

        // ===== CÁRCELES para Excel =====

        $carcelesLikeRaw = "$norm LIKE '%CCP%' OR $norm LIKE '%CPL%'";

        $carcelesDetQuery = DB::table($table)
            ->selectRaw("
                $zonaExpr     AS zona,
                $subzonaExpr  AS subzona,
                $distritoExpr AS distrito,
                $col          AS nomenclatura,
                $gradoCol     AS grado,
                COUNT(*)      AS cantidad
            ")
            ->whereRaw($onlyNdescWhere)
            ->where(function ($q) use ($carcelesLikeRaw) {
                $q->whereRaw($carcelesLikeRaw);
            });

        $carcelesDetQuery = $applyGradoFilter($carcelesDetQuery);

        $carcelesDet = $carcelesDetQuery
            ->groupBy(DB::raw($zonaExpr))
            ->groupBy(DB::raw($subzonaExpr))
            ->groupBy(DB::raw($distritoExpr))
            ->groupBy($col)
            ->groupBy($gradoCol)
            ->orderBy($col)
            ->get();

        $carcelesMap = [];
        foreach ($carcelesDet as $r) {
            $key = ($r->zona ?? '') . '|' . ($r->subzona ?? '') . '|' . ($r->distrito ?? '') . '|' . $r->nomenclatura;
            if (!isset($carcelesMap[$key])) {
                $carcelesMap[$key] = [
                    'Zona'         => $r->zona,
                    'Subzona'      => $r->subzona,
                    'Distrito'     => $r->distrito,
                    'Nomenclatura' => $r->nomenclatura,
                    'Total'        => 0,
                    'por_grado'    => [],
                ];
            }
            $carcelesMap[$key]['Total'] += (int) $r->cantidad;
            $g = $r->grado ?? '(SIN GRADO)';
            if (!isset($carcelesMap[$key]['por_grado'][$g])) {
                $carcelesMap[$key]['por_grado'][$g] = 0;
            }
            $carcelesMap[$key]['por_grado'][$g] += (int) $r->cantidad;
        }

        $carcelesGrados = [];
        foreach ($carcelesMap as $row) {
            foreach (array_keys($row['por_grado']) as $g) {
                $carcelesGrados[$g] = true;
            }
        }
        $carcelesGrados = array_keys($carcelesGrados);
        sort($carcelesGrados);

        $carcelesExcel = [];
        foreach ($carcelesMap as $row) {
            $base = [
                'Zona'         => $row['Zona'],
                'Subzona'      => $row['Subzona'],
                'Distrito'     => $row['Distrito'],
                'Nomenclatura' => $row['Nomenclatura'],
                'Total'        => $row['Total'],
            ];
            foreach ($carcelesGrados as $g) {
                $base[$g] = $row['por_grado'][$g] ?? 0;
            }
            $carcelesExcel[] = $base;
        }

        // ===== UNIDADES para Excel =====

        $troncoExpr = "
            CASE
                WHEN $norm LIKE 'NAP-DNAIS-%'
                  OR $norm LIKE 'NAP-DNED-%'
                  OR $norm LIKE 'NOPERA-%'
                THEN SUBSTRING_INDEX($norm, '-', 3)
                ELSE SUBSTRING_INDEX($norm, '-', 2)
            END
        ";

        $normOrg   = "UPPER(REGEXP_REPLACE(TRIM(nomenclatura_organico), '-+', '-'))";
        $troncoOrg = "
            CASE
                WHEN $normOrg LIKE 'NAP-DNAIS-%'
                  OR $normOrg LIKE 'NAP-DNED-%'
                  OR $normOrg LIKE 'NOPERA-%'
                THEN SUBSTRING_INDEX($normOrg, '-', 3)
                ELSE SUBSTRING_INDEX($normOrg, '-', 2)
            END
        ";

        $otrasBase = $applyGradoFilter(
            DB::table($table)
                ->selectRaw("$troncoExpr AS tronco, COUNT(*) AS seleccionados")
                ->whereRaw("$norm NOT LIKE 'NDESC-%'")
                ->whereRaw("$norm NOT LIKE '%CCP%'")
                ->whereRaw("$norm NOT LIKE '%CPL%'")
        )
            ->groupBy(DB::raw($troncoExpr))
            ->orderBy('tronco')
            ->get();

        $orgOtrasQuery = DB::table('reporte_organico')
            ->selectRaw("$troncoOrg AS tronco, SUM(numero_organico_ideal) AS aprobado")
            ->whereRaw("$normOrg NOT LIKE 'NDESC-%'")
            ->whereRaw("$normOrg NOT LIKE '%CCP%'")
            ->whereRaw("$normOrg NOT LIKE '%CPL%'");

        if (!empty($gradosSeleccionados)) {
            $orgOtrasQuery->whereIn(DB::raw('UPPER(TRIM(grado_organico))'), array_map('strtoupper', $gradosSeleccionados));
        }

        $orgOtras = $orgOtrasQuery
            ->groupBy(DB::raw($troncoOrg))
            ->get();

        $orgOtrasMap = [];
        foreach ($orgOtras as $o) {
            $orgOtrasMap[$o->tronco] = (int) $o->aprobado;
        }

        $otrasExcel = $otrasBase->map(function ($row) use ($orgOtrasMap) {
            $tronco    = $row->tronco;
            $efectivo  = (int) $row->seleccionados;
            $aprobado  = (int) ($orgOtrasMap[$tronco] ?? 0);
            $diff      = $aprobado - $efectivo;
            return [
                'Tronco'     => $tronco,
                'Aprobado'   => $aprobado,
                'Efectivo'   => $efectivo,
                'Diferencia' => $diff,
            ];
        })->values()->all();

        // ===== EXPORT MULTI-HOJA =====

        $filename = 'numericos_nomenclatura_efectiva_'.now()->format('Ymd_His').'.xlsx';

        $export = new class($zonas, $zonasSinSz, $subzonas, $distritos, $consolidadoCp, $carcelesExcel, $carcelesGrados, $otrasExcel)
            implements WithMultipleSheets
        {
            public function __construct(
                private array $zonas,
                private array $zonasSinSz,
                private array $subzonas,
                private array $distritos,
                private array $consolidadoCp,
                private array $carcelesExcel,
                private array $carcelesGrados,
                private array $otrasExcel
            ) {}

            public function sheets(): array
            {
                $makeSheet = fn(string $title, array $headings, array $rows)
                => new class($title, $headings, $rows)
                    implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
                {
                    public function __construct(
                        private string $title,
                        private array $headings,
                        private array $rows
                    ) {}
                    public function title(): string   { return $this->title; }
                    public function headings(): array { return $this->headings; }
                    public function array(): array    { return $this->rows; }
                };

                // Headings Cárceles
                $carcelesHeadings = [];
                if (!empty($this->carcelesExcel)) {
                    $carcelesHeadings = array_keys($this->carcelesExcel[0]);
                } else {
                    $carcelesHeadings = ['Zona','Subzona','Distrito','Nomenclatura','Total'];
                }

                // Headings Unidades
                $otrasHeadings = [];
                if (!empty($this->otrasExcel)) {
                    $otrasHeadings = array_keys($this->otrasExcel[0]);
                } else {
                    $otrasHeadings = ['Tronco','Aprobado','Efectivo','Diferencia'];
                }

                return [
                    $makeSheet('01_Zonas',        ['Zona','Cantidad'],                                $this->zonas),
                    $makeSheet('02_Zonas_sin_SZ', ['Zona','Cantidad'],                                $this->zonasSinSz),
                    $makeSheet('03_Subzonas',     ['Subzona','Cantidad'],                             $this->subzonas),
                    $makeSheet('04_Distritos',    ['Distrito','Cantidad'],                            $this->distritos),
                    $makeSheet('05_Consolidado',  ['Zona','Subzona','Distrito','Total','Of. Superiores','Of. Subalternos','Clases y Policías'], $this->consolidadoCp),
                    $makeSheet('06_Carceles',     $carcelesHeadings,                                  $this->carcelesExcel),
                    $makeSheet('07_Unidades',     $otrasHeadings,                                     $this->otrasExcel),
                ];
            }
        };

        return Excel::download($export, $filename);
    }
}
