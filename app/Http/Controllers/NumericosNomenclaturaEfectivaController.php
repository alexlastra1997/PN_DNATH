<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NumericosNomenclaturaEfectivaExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class NumericosNomenclaturaEfectivaController extends Controller
{
    /**
     * Vista de numéricos (solo nomenclaturas que comienzan con NDESC-).
     * Zona = NDESC-Z1..9; Subzona si existe token -SZ-; Distrito si existe token -D- (ignora D-DP).
     */
    public function index(Request $request)
    {
        $table = 'usuarios';
        $col   = 'nomenclatura_efectiva';

        // Normalización (MySQL 8+)
        $norm = "UPPER(REGEXP_REPLACE(TRIM($col), '-+', '-'))";

        // Tokens
        $zonaExpr     = "REGEXP_SUBSTR($norm, 'NDESC-Z[1-9]')"; // Z1..Z9
        $hasSzExpr    = "$norm LIKE '%-SZ-%'";
        $subzonaExpr  = "CASE WHEN $hasSzExpr THEN REGEXP_SUBSTR($norm, 'SZ-[^-]+') END";
        $hasDExpr     = "$norm LIKE '%-D-%'";
        $distritoRaw  = "CASE WHEN $hasDExpr THEN REGEXP_SUBSTR($norm, 'D-[^-]+') END";
        $distritoExpr = "NULLIF($distritoRaw, 'D-DP')"; // ignora D-DP

        // Solo NDESC-
        $onlyNdescWhere = "$norm LIKE 'NDESC-%'";

        // ===== KPIs / Tablas base =====
        $totalFilas = DB::table($table)
            ->whereRaw($onlyNdescWhere)
            ->count();

        // Zonas (todas)
        $porZonas = DB::table($table)
            ->selectRaw("$zonaExpr AS zona, COUNT(*) AS cantidad")
            ->whereRaw($onlyNdescWhere)
            ->groupBy(DB::raw($zonaExpr))
            ->orderBy('zona')
            ->get();

        // Zonas (solo sin SZ-)
        $porZonasSoloSinSZ = DB::table($table)
            ->selectRaw("$zonaExpr AS zona, COUNT(*) AS cantidad")
            ->whereRaw($onlyNdescWhere)
            ->whereRaw("($hasSzExpr) = 0")
            ->groupBy(DB::raw($zonaExpr))
            ->orderBy('zona')
            ->get();

        $totalSinSZ = DB::table($table)
            ->whereRaw($onlyNdescWhere)
            ->whereRaw("($hasSzExpr) = 0")
            ->count();

        // Subzonas (con SZ-)
        $porSubzonas = DB::table($table)
            ->selectRaw("$subzonaExpr AS subzona, COUNT(*) AS cantidad")
            ->whereRaw($onlyNdescWhere)
            ->whereRaw("($hasSzExpr) = 1")
            ->groupBy(DB::raw($subzonaExpr))
            ->orderBy('subzona')
            ->get();

        // Distritos (con D- válido)
        $porDistritos = DB::table($table)
            ->selectRaw("$distritoExpr AS distrito, COUNT(*) AS cantidad")
            ->whereRaw($onlyNdescWhere)
            ->whereRaw("($hasDExpr) = 1")
            ->whereRaw("$distritoExpr IS NOT NULL AND $distritoExpr <> ''")
            ->groupBy(DB::raw($distritoExpr))
            ->orderBy('distrito')
            ->get();

        // Consolidado base
        $consolidado = DB::table($table)
            ->selectRaw("$zonaExpr AS zona, $subzonaExpr AS subzona, $distritoExpr AS distrito, COUNT(*) AS cantidad")
            ->whereRaw($onlyNdescWhere)
            ->groupBy(DB::raw("$zonaExpr, $subzonaExpr, $distritoExpr"))
            ->orderBy('zona')->orderBy('subzona')->orderBy('distrito')
            ->get();

        // ===== Pivot por cuadro_policial (Of. Superiores / Of. Subalternos / Clases y Policías) =====
        $porGrupoYCuadro = DB::table($table)
            ->selectRaw("$zonaExpr AS zona, $subzonaExpr AS subzona, $distritoExpr AS distrito, UPPER(TRIM(cuadro_policial)) AS cp, COUNT(*) AS cnt")
            ->whereRaw($onlyNdescWhere)
            ->groupBy(DB::raw("$zonaExpr, $subzonaExpr, $distritoExpr, UPPER(TRIM(cuadro_policial))"))
            ->get();

        // Index por grupo
        $map = [];
        foreach ($porGrupoYCuadro as $r) {
            $key = ($r->zona ?? '') . '|' . ($r->subzona ?? '') . '|' . ($r->distrito ?? '');
            if (!isset($map[$key])) $map[$key] = [];
            $map[$key][$r->cp ?: ''] = (int) $r->cnt;
        }

        // Normalizador y clasificadores
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

        $consolidadoCp = $consolidado->map(function ($row) use ($map, $isSup, $isSub, $isClasesPol) {
            $key = ($row->zona ?? '') . '|' . ($row->subzona ?? '') . '|' . ($row->distrito ?? '');
            $buck = $map[$key] ?? [];

            $sup = 0; $sub = 0; $clp = 0;
            foreach ($buck as $cp => $cnt) {
                if     ($isSup($cp))       $sup += $cnt;
                elseif ($isSub($cp))       $sub += $cnt;
                elseif ($isClasesPol($cp)) $clp += $cnt;
            }

            $row->sup   = $sup; // Oficiales Superiores
            $row->sub   = $sub; // Oficiales Subalternos
            $row->clpol = $clp; // Clases y Policías
            return $row;
        });

        // KPIs
        $zonasUnicas     = $porZonas->filter(fn($z) => !empty($z->zona))->count();
        $subzonasUnicas  = $porSubzonas->filter(fn($s) => !empty($s->subzona))->count();
        $distritosUnicos = $porDistritos->filter(fn($d) => !empty($d->distrito))->count();

        return view('numericos.nomenclatura_efectiva', compact(
            'totalFilas',
            'porZonas',
            'porZonasSoloSinSZ',
            'totalSinSZ',
            'porSubzonas',
            'porDistritos',
            'consolidadoCp',
            'zonasUnicas',
            'subzonasUnicas',
            'distritosUnicos'
        ))->with('soloNdesc', true);
    }

    /**
     * Exporta a Excel con 5 hojas:
     * 01 Zonas, 02 Zonas sin SZ, 03 Subzonas, 04 Distritos, 05 Consolidado (Total + Sup/Sub/Clases-Policías)
     */
    public function export(Request $request)
    {
        $table = 'usuarios';
        $col   = 'nomenclatura_efectiva';

        // Normalización (MySQL 8+)
        $norm = "UPPER(REGEXP_REPLACE(TRIM($col), '-+', '-'))";

        // Tokens / reglas
        $zonaExpr     = "REGEXP_SUBSTR($norm, 'NDESC-Z[1-9]')";   // Z1..Z9 (ajusta a Z[0-9]+ si tienes Z10+)
        $hasSzExpr    = "$norm LIKE '%-SZ-%'";
        $subzonaExpr  = "CASE WHEN $hasSzExpr THEN REGEXP_SUBSTR($norm, 'SZ-[^-]+') END";
        $hasDExpr     = "$norm LIKE '%-D-%'";
        $distritoRaw  = "CASE WHEN $hasDExpr THEN REGEXP_SUBSTR($norm, 'D-[^-]+') END";
        $distritoExpr = "NULLIF($distritoRaw, 'D-DP')";           // ignora D-DP
        $onlyNdescWhere = "$norm LIKE 'NDESC-%'";

        // ===== Tablas para Excel (arrays simples) =====
        $zonas = DB::table($table)
            ->selectRaw("$zonaExpr AS zona, COUNT(*) AS cantidad")
            ->whereRaw($onlyNdescWhere)
            ->groupBy(DB::raw($zonaExpr))
            ->orderBy('zona')
            ->get()
            ->map(fn($r) => ['Zona' => $r->zona, 'Cantidad' => (int)$r->cantidad])
            ->values()->all();

        $zonasSinSz = DB::table($table)
            ->selectRaw("$zonaExpr AS zona, COUNT(*) AS cantidad")
            ->whereRaw($onlyNdescWhere)
            ->whereRaw("($hasSzExpr) = 0")
            ->groupBy(DB::raw($zonaExpr))
            ->orderBy('zona')
            ->get()
            ->map(fn($r) => ['Zona' => $r->zona, 'Cantidad' => (int)$r->cantidad])
            ->values()->all();

        $subzonas = DB::table($table)
            ->selectRaw("$subzonaExpr AS subzona, COUNT(*) AS cantidad")
            ->whereRaw($onlyNdescWhere)
            ->whereRaw("($hasSzExpr) = 1")
            ->groupBy(DB::raw($subzonaExpr))
            ->orderBy('subzona')
            ->get()
            ->map(fn($r) => ['Subzona' => $r->subzona, 'Cantidad' => (int)$r->cantidad])
            ->values()->all();

        $distritos = DB::table($table)
            ->selectRaw("$distritoExpr AS distrito, COUNT(*) AS cantidad")
            ->whereRaw($onlyNdescWhere)
            ->whereRaw("($hasDExpr) = 1")
            ->whereRaw("$distritoExpr IS NOT NULL AND $distritoExpr <> ''")
            ->groupBy(DB::raw($distritoExpr))
            ->orderBy('distrito')
            ->get()
            ->map(fn($r) => ['Distrito' => $r->distrito, 'Cantidad' => (int)$r->cantidad])
            ->values()->all();

        // Consolidado base
        $consolidado = DB::table($table)
            ->selectRaw("$zonaExpr AS zona, $subzonaExpr AS subzona, $distritoExpr AS distrito, COUNT(*) AS cantidad")
            ->whereRaw($onlyNdescWhere)
            ->groupBy(DB::raw("$zonaExpr, $subzonaExpr, $distritoExpr"))
            ->orderBy('zona')->orderBy('subzona')->orderBy('distrito')
            ->get();

        // Conteos por cuadro_policial para cada grupo
        $porGrupoYCuadro = DB::table($table)
            ->selectRaw("$zonaExpr AS zona, $subzonaExpr AS subzona, $distritoExpr AS distrito, UPPER(TRIM(cuadro_policial)) AS cp, COUNT(*) AS cnt")
            ->whereRaw($onlyNdescWhere)
            ->groupBy(DB::raw("$zonaExpr, $subzonaExpr, $distritoExpr, UPPER(TRIM(cuadro_policial))"))
            ->get();

        // Index por grupo
        $map = [];
        foreach ($porGrupoYCuadro as $r) {
            $key = ($r->zona ?? '') . '|' . ($r->subzona ?? '') . '|' . ($r->distrito ?? '');
            if (!isset($map[$key])) $map[$key] = [];
            $map[$key][$r->cp ?: ''] = (int)$r->cnt;
        }

        // Normalizador y clasificadores (Sup / Sub / Clases+Policías)
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

        $consolidadoCp = $consolidado->map(function ($row) use ($map, $isSup, $isSub, $isClasesPol) {
            $key = ($row->zona ?? '') . '|' . ($row->subzona ?? '') . '|' . ($row->distrito ?? '');
            $buck = $map[$key] ?? [];

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
                'Total'             => (int)$row->cantidad,
                'Of. Superiores'    => $sup,
                'Of. Subalternos'   => $sub,
                'Clases y Policías' => $clp,
            ];
        })->values()->all();

        // ===== Export con clases anónimas (sin App\Exports) =====
        $filename = 'numericos_nomenclatura_efectiva_'.now()->format('Ymd_His').'.xlsx';

        $export = new class($zonas, $zonasSinSz, $subzonas, $distritos, $consolidadoCp)
            implements WithMultipleSheets
        {
            public function __construct(
                private array $zonas,
                private array $zonasSinSz,
                private array $subzonas,
                private array $distritos,
                private array $consolidadoCp
            ) {}

            public function sheets(): array
            {
                // helper: clase anónima de hoja simple
                $makeSheet = fn(string $title, array $headings, array $rows)
                => new class($title, $headings, $rows)
                    implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
                {
                    public function __construct(
                        private string $title,
                        private array $headings,
                        private array $rows
                    ) {}
                    public function title(): string     { return $this->title; }
                    public function headings(): array   { return $this->headings; }
                    public function array(): array      { return $this->rows; }
                };

                return [
                    $makeSheet('01_Zonas',        ['Zona','Cantidad'],                                $this->zonas),
                    $makeSheet('02_Zonas_sin_SZ', ['Zona','Cantidad'],                                $this->zonasSinSz),
                    $makeSheet('03_Subzonas',     ['Subzona','Cantidad'],                             $this->subzonas),
                    $makeSheet('04_Distritos',    ['Distrito','Cantidad'],                            $this->distritos),
                    $makeSheet('05_Consolidado',  ['Zona','Subzona','Distrito','Total','Of. Superiores','Of. Subalternos','Clases y Policías'], $this->consolidadoCp),
                ];
            }
        };

        return Excel::download($export, $filename);
    }
}
