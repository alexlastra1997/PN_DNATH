<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MapaNdescExport;

class MapaNdescController extends Controller
{
    // Z1..Z7: requiere SZ-
    private const REG_INDEX_SZ  = '/^NDESC\-Z([1-7])\-SZ\-([A-ZÁÉÍÓÚÑ0-9 ]+)\b/i';
    // Z8..Z9: sin SZ-
    private const REG_INDEX_Z89 = '/^NDESC\-Z([89])\-([A-ZÁÉÍÓÚÑ0-9 ]+)\b/i';
    // Distrito: -D-{NOMBRE}-  (captura hasta el siguiente - o fin)
    private const REG_DISTRITO  = '/-D-([A-ZÁÉÍÓÚÑ0-9 ]+?)(?:-|$)/i';

    /** Normaliza TEXTO: mayúsculas, sin acentos, sin dobles espacios */
    private static function norm(string $s): string
    {
        $s = trim($s ?? '');
        $s = mb_strtoupper($s, 'UTF-8');
        $s = strtr($s, ['Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U','Ñ'=>'N']);
        $s = preg_replace('/\s+/u', ' ', $s);
        return $s;
    }

    /** Normaliza GRADO: mayúsculas, sin acentos ni signos (.,-/ espacios) */
    private static function normGrade(string $s): string
    {
        $s = trim($s ?? '');
        $s = mb_strtoupper($s, 'UTF-8');
        $s = strtr($s, ['Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U','Ñ'=>'N']);
        // Conserva solo A-Z y 0-9 (CPTN., CPTN / TNTE -> CPTN / TNTE → tokens luego)
        $s = preg_replace('/[^A-Z0-9]/u', '', $s);
        return $s ?: '';
    }

    private function fromSlug(string $slug): string
    {
        $base = str_replace('-', ' ', $slug);
        return mb_strtoupper($base, 'UTF-8');
    }

    private function buildPrefix(int $zona, string $subzona): string
    {
        return $zona >= 8 ? "NDESC-Z{$zona}-{$subzona}-" : "NDESC-Z{$zona}-SZ-{$subzona}-";
    }

    private function getDistrito(?string $nomen): string
    {
        if ($nomen && preg_match(self::REG_DISTRITO, $nomen, $m)) {
            return self::norm($m[1]); // normalizado y sin guion
        }
        return 'SIN DISTRITO';
    }

    /** ---------- INDEX: mapa de subzonas ---------- */
    public function index(Request $request)
    {
        $rows = DB::table('reporte_organico')
            ->select('nomenclatura_organico')
            ->whereNotNull('nomenclatura_organico')
            ->where('nomenclatura_organico', 'like', 'NDESC-Z%')
            ->get();

        $pares = [];
        foreach ($rows as $r) {
            $nom = trim((string)$r->nomenclatura_organico);

            if (preg_match(self::REG_INDEX_SZ, $nom, $m)) {
                $z  = (int)$m[1];
                $sz = self::norm($m[2]);
                $pares["Z{$z}"][$sz] = true;
                continue;
            }
            if (preg_match(self::REG_INDEX_Z89, $nom, $m)) {
                $z  = (int)$m[1];
                $sz = self::norm($m[2]);
                $pares["Z{$z}"][$sz] = true;
            }
        }

        $zonas = [];
        for ($z = 1; $z <= 9; $z++) {
            $key = "Z{$z}";
            $szs = array_key_exists($key, $pares) ? array_keys($pares[$key]) : [];
            sort($szs, SORT_NATURAL | SORT_FLAG_CASE);
            $zonas[$key] = $szs;
        }

        return view('mapa-ndesc.index', compact('zonas'));
    }

    /** ---------- Lógica compartida: show() y export() ---------- */
    private function collectData(int $zona, string $subzonaSlug): array
    {
        $subzonaNombre = $this->fromSlug($subzonaSlug);
        $prefix = $this->buildPrefix($zona, $subzonaNombre); // LIKE este prefijo%

        // 1) ORGÁNICO (filtrado a roles objetivo)
        $organicoRowsAll = DB::table('reporte_organico')
            ->select('nomenclatura_organico', 'cargo_organico', 'grado_organico', 'numero_organico_ideal')
            ->where('nomenclatura_organico', 'like', $prefix.'%')
            ->whereNotNull('cargo_organico')
            ->get();

        $organicoRows = $organicoRowsAll->filter(function ($r) {
            $c = mb_strtoupper($r->cargo_organico ?? '', 'UTF-8');
            return preg_match('/(^|[^A-ZÁÉÍÓÚÑ])COMANDANTE([^A-ZÁÉÍÓÚÑ]|$)/u', $c)
                || preg_match('/(^|[^A-ZÁÉÍÓÚÑ])SUBCOMANDANTE([^A-ZÁÉÍÓÚÑ]|$)/u', $c)
                || preg_match('/(^|[^A-ZÁÉÍÓÚÑ])SUBJEFE(A)?([^A-ZÁÉÍÓÚÑ]|$)/u', $c)
                || preg_match('/(^|[^A-ZÁÉÍÓÚÑ])JEFE(A)?([^A-ZÁÉÍÓÚÑ]|$)/u', $c);
        })->values();

        // Ideal y requisitos por [Distrito][CargoNormalizado]
        $orgPorDistritoCargo = [];
        $requisitosGrado = []; // [dist][cargoKey][GRADE] => true

        foreach ($organicoRows as $r) {
            $dist = $this->getDistrito($r->nomenclatura_organico);
            $cargoKey = self::norm($r->cargo_organico ?? '');
            $orgPorDistritoCargo[$dist][$cargoKey] = ($orgPorDistritoCargo[$dist][$cargoKey] ?? 0)
                + (int)($r->numero_organico_ideal ?? 0);

            // tokeniza grado_organico (ej. "CPTN / TNTE, SBTE")
            $gradoOrgRaw = (string)($r->grado_organico ?? '');
            if ($gradoOrgRaw !== '') {
                $tokens = preg_split('/[\/,;|\'"\s]+/u', $gradoOrgRaw, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($tokens as $tok) {
                    $g = self::normGrade($tok);
                    if ($g !== '') {
                        $requisitosGrado[$dist][$cargoKey][$g] = true; // set-like
                    }
                }
            }
        }

        // 2) USUARIOS
        $usuarios = DB::table('usuarios')
            ->select('cedula','apellidos_nombres','grado','promocion','funcion_efectiva',
                'fecha_efectiva','nomenclatura_efectiva','estado_efectivo')
            ->where('nomenclatura_efectiva', 'like', $prefix.'%')
            ->where(function ($q) {
                $q->whereRaw("UPPER(funcion_efectiva) COLLATE utf8mb4_general_ci REGEXP '(^|[^A-ZÁÉÍÓÚÑ])COMANDANTE([^A-ZÁÉÍÓÚÑ]|$)'")
                    ->orWhereRaw("UPPER(funcion_efectiva) COLLATE utf8mb4_general_ci REGEXP '(^|[^A-ZÁÉÍÓÚÑ])SUBCOMANDANTE([^A-ZÁÉÍÓÚÑ]|$)'")
                    ->orWhereRaw("UPPER(funcion_efectiva) COLLATE utf8mb4_general_ci REGEXP '(^|[^A-ZÁÉÍÓÚÑ])SUBJEFE(A)?([^A-ZÁÉÍÓÚÑ]|$)'")
                    ->orWhereRaw("UPPER(funcion_efectiva) COLLATE utf8mb4_general_ci REGEXP '(^|[^A-ZÁÉÍÓÚÑ])JEFE(A)?([^A-ZÁÉÍÓÚÑ]|$)'");
            })
            ->orderBy('funcion_efectiva')
            ->orderBy('grado')
            ->orderBy('apellidos_nombres')
            ->get();

        // Agrupar por distrito (normalizado)
        $usuariosPorDistrito = $usuarios->groupBy(function ($u) {
            return $this->getDistrito($u->nomenclatura_efectiva);
        });

        // Conteos actuales por [Distrito][CargoNormalizado]
        $actualPorDistritoCargo = [];
        foreach ($usuarios as $u) {
            $dist = $this->getDistrito($u->nomenclatura_efectiva);
            $cargoKey = self::norm($u->funcion_efectiva ?? '');
            $actualPorDistritoCargo[$dist][$cargoKey] = ($actualPorDistritoCargo[$dist][$cargoKey] ?? 0) + 1;
        }

        // 3) Estados por distrito & cargo
        $distritosKeys = array_unique(array_merge(array_keys($orgPorDistritoCargo), array_keys($actualPorDistritoCargo)));
        usort($distritosKeys, function ($a, $b) {
            if ($a === 'SIN DISTRITO') return -1;
            if ($b === 'SIN DISTRITO') return 1;
            return strnatcasecmp($a, $b);
        });

        $estadoPorDistrito = [];
        foreach ($distritosKeys as $dist) {
            $orgCargos  = $orgPorDistritoCargo[$dist]   ?? [];
            $actCargos  = $actualPorDistritoCargo[$dist]?? [];
            $allCargosD = array_unique(array_merge(array_keys($orgCargos), array_keys($actCargos)));
            sort($allCargosD, SORT_NATURAL | SORT_FLAG_CASE);

            foreach ($allCargosD as $cargoKey) {
                $ideal  = (int)($orgCargos[$cargoKey] ?? 0);
                $actual = (int)($actCargos[$cargoKey] ?? 0);
                $estado = $actual > $ideal ? 'EXCEDIDO' : ($actual === $ideal ? 'COMPLETO' : 'VACANTE');
                $estadoPorDistrito[$dist][] = [
                    'cargo'  => $cargoKey,
                    'ideal'  => $ideal,
                    'actual' => $actual,
                    'estado' => $estado,
                ];
            }
        }

        // 4) Líderes subzona (comandante/subcomandante) y resto
        $listaSubzona = $usuariosPorDistrito->get('SIN DISTRITO', collect());
        $esLiderSubzona = function ($funcion) {
            $f = self::norm($funcion ?? '');
            return str_contains($f, 'COMANDANTE SUBZONAL DE POLICIA')
                || str_contains($f, 'SUBCOMANDANTE SUBZONAL DE POLICIA');
        };
        $leadersSubzona = $listaSubzona->filter(fn($u) => $esLiderSubzona($u->funcion_efectiva))
            ->sortBy([
                fn($u) => str_starts_with(self::norm($u->funcion_efectiva ?? ''), 'COMANDANTE') ? 0 : 1,
                fn($u) => $u->grado,
                fn($u) => $u->apellidos_nombres,
            ])->values();
        $restoSubzona = $listaSubzona->reject(fn($u) => $esLiderSubzona($u->funcion_efectiva))->values();

        $usuariosPorDistrito = $usuariosPorDistrito->put('SIN DISTRITO', $restoSubzona)
            ->sortKeysUsing(function ($a, $b) {
                if ($a === 'SIN DISTRITO') return -1;
                if ($b === 'SIN DISTRITO') return 1;
                return strnatcasecmp($a, $b);
            });

        return [
            'zona'                => $zona,
            'subzonaNombre'       => $subzonaNombre,
            'usuariosPorDistrito' => $usuariosPorDistrito,
            'estadoPorDistrito'   => $estadoPorDistrito,
            'leadersSubzona'      => $leadersSubzona,
            'requisitosGrado'     => $requisitosGrado, // ya tokenizado/normalizado
        ];
    }

    /** ---------- SHOW ---------- */
    public function show(int $zona, string $subzonaSlug)
    {
        $data = $this->collectData($zona, $subzonaSlug);
        return view('mapa-ndesc.show', $data);
    }

    /** ---------- EXPORT EXCEL (una hoja por tab) ---------- */
    public function export(int $zona, string $subzonaSlug)
    {
        $data = $this->collectData($zona, $subzonaSlug);
        $file = "NDESC-Z{$zona}-{$data['subzonaNombre']}.xlsx";
        return Excel::download(new MapaNdescExport($data), $file);
    }
}
