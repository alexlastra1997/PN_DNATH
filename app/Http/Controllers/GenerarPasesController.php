<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class GenerarPasesController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 50);

        // ===== Catálogos =====
        $gradosOrdenados = DB::table('usuarios')
            ->whereNotNull('grado')->where('grado','<>','')
            ->distinct()->orderBy('grado')->pluck('grado')->values();

        $sexos = DB::table('usuarios')
            ->whereNotNull('sexo')->where('sexo','<>','')
            ->distinct()->orderBy('sexo')->pluck('sexo')->values();

        $tiposPersonal = DB::table('usuarios')
            ->whereNotNull('tipo_personal')->where('tipo_personal','<>','')
            ->distinct()->orderBy('tipo_personal')->pluck('tipo_personal')->values();

        $promociones = DB::table('usuarios')
            ->whereNotNull('promocion')->where('promocion','<>','')
            ->distinct()->orderBy('promocion')->pluck('promocion')->values();

        $provinciasTrab = DB::table('usuarios')
            ->whereNotNull('provincia_trabaja')->where('provincia_trabaja','<>','')
            ->distinct()->orderBy('provincia_trabaja')->pluck('provincia_trabaja')->values();

        $nomenclaturas = DB::table('usuarios')
            ->whereNotNull('nomenclatura_efectiva')->where('nomenclatura_efectiva','<>','')
            ->distinct()->orderBy('nomenclatura_efectiva')->pluck('nomenclatura_efectiva')->values();

        $funciones = DB::table('usuarios')
            ->whereNotNull('funcion_efectiva')->where('funcion_efectiva','<>','')
            ->distinct()->orderBy('funcion_efectiva')->pluck('funcion_efectiva')->values();

        $estadosEfectivos = DB::table('usuarios')
            ->whereNotNull('estado_efectivo')->where('estado_efectivo','<>','')
            ->distinct()->orderBy('estado_efectivo')->pluck('estado_efectivo')->values();

        // ===== Catálogo unificado de FASE MATERNIDAD O LACTANCIA =====
        $fm1 = DB::table('usuarios')
            ->whereNotNull('FaseMaternidadUDGA')->where('FaseMaternidadUDGA','<>','')
            ->distinct()->pluck('FaseMaternidadUDGA');
        $fm2 = DB::table('usuarios')
            ->whereNotNull('fase_maternidad')->where('fase_maternidad','<>','')
            ->distinct()->pluck('fase_maternidad');
        $faseMLCatalog = $fm1->merge($fm2)
            ->map(fn($v)=>trim((string)$v))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // ===== Catálogo de alertas (hash -> label) =====
        [$alertCatalog, $alertKeyToLabel] = $this->buildAlertCatalog();

        // ===== Rehidratación =====
        $promSel        = Arr::wrap($request->query('promocion', []));
        $provTrabSel    = Arr::wrap($request->query('provincia_trabaja', []));
        $flagsSel       = Arr::wrap($request->query('flags', []));
        $alertsSelKeys  = Arr::wrap($request->query('alertas_sel_key', []));
        $fechaSel       = Arr::wrap($request->query('fecha_efectiva_bucket', []));
        $nomSel         = Arr::wrap($request->query('nomenclatura_efectiva', []));
        $funSel         = Arr::wrap($request->query('funcion_efectiva', []));
        $estSel         = Arr::wrap($request->query('estado_efectivo', []));
        $faseMLSel      = Arr::wrap($request->query('fase_ml', [])); // << NUEVO (se mantiene)

        // ===== Query base =====
        $base = DB::table('usuarios');

        if ($v = trim($request->query('cedula', ''))) {
            $base->where('cedula', 'like', "%{$v}%");
        }
        if ($v = trim($request->query('apellidos_nombres', ''))) {
            $like = '%'.preg_replace('/\s+/', '%', $v).'%';
            $base->where('apellidos_nombres', 'like', $like);
        }
        if ($v = $request->query('grado'))         $base->where('grado', $v);
        if ($v = $request->query('sexo'))          $base->where('sexo', $v);
        if ($v = $request->query('tipo_personal')) $base->where('tipo_personal', $v);

        if (!empty($promSel))     $base->whereIn('promocion', $promSel);
        if (!empty($provTrabSel)) $base->whereIn('provincia_trabaja', $provTrabSel);

        // ===== Antigüedad (fecha_efectiva) buckets (OR) =====
        if (!empty($fechaSel)) {
            $now = Carbon::now();
            $d1  = $now->copy()->subYear()->toDateString();
            $d2  = $now->copy()->subYears(2)->toDateString();
            $d5  = $now->copy()->subYears(5)->toDateString();

            $base->where(function($q) use ($fechaSel, $d1, $d2, $d5) {
                if (in_array('lt1', $fechaSel, true))  $q->orWhere('fecha_efectiva', '>',  $d1); // < 1 año
                if (in_array('gte1', $fechaSel, true)) $q->orWhere('fecha_efectiva', '<=', $d1); // ≥ 1 año
                if (in_array('gte2', $fechaSel, true)) $q->orWhere('fecha_efectiva', '<=', $d2); // ≥ 2 años
                if (in_array('gte5', $fechaSel, true)) $q->orWhere('fecha_efectiva', '<=', $d5); // ≥ 5 años
            });
        }

        // ===== Nomenclatura/Función/Estado (múltiples) =====
        if (!empty($nomSel)) $base->whereIn('nomenclatura_efectiva', $nomSel);
        if (!empty($funSel)) $base->whereIn('funcion_efectiva', $funSel);
        if (!empty($estSel)) $base->whereIn('estado_efectivo', $estSel);

        // ===== EXCLUIR por ALERTAS (cualquiera) =====
        $alertTexts = [];
        foreach ($alertsSelKeys as $k) if (isset($alertKeyToLabel[$k])) $alertTexts[] = $alertKeyToLabel[$k];

        if (!empty($alertTexts)) {
            $base->where(function($AND) use ($alertTexts) {
                foreach ($alertTexts as $txt) {
                    $like = '%'.$txt.'%';
                    $AND->where(function($no) use ($like) {
                        $no->where(function($c) use ($like) {
                            $c->whereNull('alertas')
                                ->orWhereRaw("UPPER(TRIM(COALESCE(alertas,''))) NOT LIKE UPPER(?)", [$like]);
                        });
                        $no->where(function($c) use ($like) {
                            $c->whereNull('alerta_devengacion')
                                ->orWhereRaw("UPPER(TRIM(COALESCE(alerta_devengacion,''))) NOT LIKE UPPER(?)", [$like]);
                        });
                        $no->where(function($c) use ($like) {
                            $c->whereNull('alerta_marco_legal')
                                ->orWhereRaw("UPPER(TRIM(COALESCE(alerta_marco_legal,''))) NOT LIKE UPPER(?)", [$like]);
                        });
                    });
                }
            });
        }

        // ===== EXCLUIR por BANDERAS (cualquiera) — robusto =====
        $flagColsAll = [
            'contrato_estudios',
            'conyuge_policia_activo',
            'enf_catast_sp',
            'enf_catast_conyuge_hijos',
            'discapacidad_sp',
            'discapacidad_conyuge_hijos',
            'novedad_situacion',
            'observacion_tenencia',
            'alertas_problemas_salud',
            // NUEVOS: fases de maternidad (excluir si tienen cualquier valor)
            'fase_maternidad',       // Fase Maternidad 1
            'FaseMaternidadUDGA',    // Fase Maternidad 2
        ];
        $flagsSel = array_values(array_intersect($flagsSel, $flagColsAll));

        if (!empty($flagsSel)) {
            $base->where(function($AND) use ($flagsSel) {
                foreach ($flagsSel as $col) {
                    $AND->where(function($no) use ($col) {
                        $expr  = "TRIM(COALESCE($col,''))";
                        $exprU = "UPPER($expr)";

                        // Fase Maternidad 1 y 2: mantener SOLO filas donde está vacío o nulo
                        if (in_array($col, ['fase_maternidad','FaseMaternidadUDGA'], true)) {
                            $no->whereNull($col)
                                ->orWhereRaw("$expr = ''");
                            return;
                        }

                        // Campos de texto que aceptan “sin novedad”
                        if (in_array($col, ['novedad_situacion','observacion_tenencia'], true)) {
                            $no->whereNull($col)
                                ->orWhereRaw("$expr = ''")
                                ->orWhereRaw("$exprU IN ('SIN NOVEDAD','SIN_NOVEDAD','NINGUNA','NINGUNO','N/A','NA')");
                            return;
                        }

                        // Resto de banderas: considerar vacíos/negativos como “no aplica”
                        $no->whereNull($col)
                            ->orWhereRaw("$expr = ''")
                            ->orWhereRaw("$exprU IN ('0','NO','N','FALSE')");
                    });
                }
            });
        }

        // ===== (NUEVO) Exportar a Excel si se solicitó =====
        $isExport = $request->query('export') === 'excel';
        $selectCols = [
            'cedula','apellidos_nombres','grado','sexo','tipo_personal',
            'fecha_efectiva','nomenclatura_efectiva','funcion_efectiva','estado_efectivo'
        ];
        $headersMap = [
            'cedula'                => 'Cédula',
            'apellidos_nombres'     => 'Apellidos Nombres',
            'grado'                 => 'Grado',
            'sexo'                  => 'Sexo',
            'tipo_personal'         => 'Tipo',
            'fecha_efectiva'        => 'Fecha efectiva',
            'nomenclatura_efectiva' => 'Nomenclatura efectiva',
            'funcion_efectiva'      => 'Función efectiva',
            'estado_efectivo'       => 'Estado efectivo',
        ];
        $exportExcel = function($rows) use ($headersMap) {
            $data = [];
            $order = array_keys($headersMap);
            foreach ($rows as $r) {
                $line = [];
                foreach ($order as $k) {
                    $line[] = data_get($r, $k, '');
                }
                $data[] = $line;
            }

            $export = new class($data, array_values($headersMap))
                implements \Maatwebsite\Excel\Concerns\FromArray,
                \Maatwebsite\Excel\Concerns\WithHeadings,
                \Maatwebsite\Excel\Concerns\ShouldAutoSize {
                public function __construct(private array $data, private array $headings) {}
                public function array(): array    { return $this->data; }
                public function headings(): array { return $this->headings; }
            };

            $filename = 'usuarios_filtrados_'.now()->format('Ymd_His').'.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
        };

        // ===== Requerimientos por grado (subset) =====
        $req = (array) $request->query('req_grados', []);
        $req = array_filter($req, fn($v) => is_numeric($v) && (int)$v > 0);

        if (!empty($req)) {
            $seleccionados = collect();
            foreach ($req as $grado => $n) {
                $n = (int) $n;
                if ($n <= 0) continue;
                $subset = (clone $base)->where('grado', $grado)
                    ->orderBy('cedula')
                    ->select($selectCols) // para export y para mantener columnas uniformes
                    ->limit($n)->get();
                $seleccionados = $seleccionados->concat($subset);
            }
            $seleccionados = $seleccionados->unique('cedula')->values();

            // Si pidieron Excel, lo descargamos con el subset seleccionado
            if ($isExport) {
                return $exportExcel($seleccionados);
            }

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $items = $seleccionados->forPage($currentPage, $perPage)->values();
            $usuarios = new LengthAwarePaginator(
                $items, $seleccionados->count(), $perPage, $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // Si pidieron Excel, exportar todo el resultado filtrado (sin paginar)
            if ($isExport) {
                $rows = (clone $base)->orderBy('cedula')->select($selectCols)->get();
                return $exportExcel($rows);
            }

            $usuarios = (clone $base)->orderBy('cedula')->paginate($perPage)->appends($request->query());
        }

        return view('generar_pases.index', [
            'usuarios'         => $usuarios,
            'gradosOrdenados'  => $gradosOrdenados,
            'sexos'            => $sexos,
            'tiposPersonal'    => $tiposPersonal,
            'promociones'      => $promociones,
            'provinciasTrab'   => $provinciasTrab,
            'nomenclaturas'    => $nomenclaturas,
            'funciones'        => $funciones,
            'estadosEfectivos' => $estadosEfectivos,
            'alertCatalog'     => $alertCatalog,
            'faseMLCatalog'    => $faseMLCatalog,   // se mantiene
            // Rehidratación
            'promSel'          => $promSel,
            'provTrabSel'      => $provTrabSel,
            'flagsSel'         => $flagsSel,
            'alertsSelKeys'    => $alertsSelKeys,
            'fechaSel'         => $fechaSel,
            'nomSel'           => $nomSel,
            'funSel'           => $funSel,
            'estSel'           => $estSel,
            'faseMLSel'        => $faseMLSel,       // se mantiene
        ]);
    }

    private function buildAlertCatalog(): array
    {
        $cols = ['alertas', 'alerta_devengacion', 'alerta_marco_legal'];

        $labels = collect();
        foreach ($cols as $c) {
            $vals = DB::table('usuarios')
                ->whereNotNull($c)->where($c,'<>','')
                ->distinct()->pluck($c);
            foreach ($vals as $v) {
                $t = trim((string)$v);
                if ($t !== '') $labels->push($t);
            }
        }
        $labels = $labels->unique()->sortBy(fn($s)=>mb_strlen($s))->values();

        $catalog = [];
        $map = [];
        foreach ($labels as $label) {
            $key = substr(sha1($label), 0, 12);
            $catalog[] = ['key'=>$key, 'label'=>$label];
            $map[$key] = $label;
        }
        return [$catalog, $map];
    }

    private function downloadExcel($rows, $filename = 'usuarios_filtrados')
    {
        // Columnas en el mismo orden de tu tabla
        $cols = [
            'cedula'                => 'Cédula',
            'apellidos_nombres'     => 'Apellidos Nombres',
            'grado'                 => 'Grado',
            'sexo'                  => 'Sexo',
            'tipo_personal'         => 'Tipo',
            'fecha_efectiva'        => 'Fecha efectiva',
            'nomenclatura_efectiva' => 'Nomenclatura efectiva',
            'funcion_efectiva'      => 'Función efectiva',
            'estado_efectivo'       => 'Estado efectivo',
        ];

        // Convertimos la colección/array de filas al arreglo que Excel requiere
        $data = [];
        foreach ($rows as $r) {
            $line = [];
            foreach (array_keys($cols) as $k) {
                $line[] = data_get($r, $k, '');
            }
            $data[] = $line;
        }

        // Export anónimo (sin crear clase aparte)
        $export = new class($data, array_values($cols)) implements FromArray, WithHeadings, ShouldAutoSize {
            public function __construct(private array $data, private array $headings) {}
            public function array(): array    { return $this->data; }
            public function headings(): array { return $this->headings; }
        };

        return Excel::download($export, $filename.'_'.now()->format('Ymd_His').'.xlsx');
    }


}
