<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use App\Models\Usuario;
use App\Models\NumDemerito;
use Illuminate\Http\Request;
use App\Exports\UsuariosExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;
use App\Models\Alerta;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Pagination\LengthAwarePaginator;



class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $cantidadCedulasUnicas = DB::table('usuarios')
            ->whereNotNull('cedula')
            ->distinct()
            ->count('cedula');

        $query = Usuario::query();

        // Filtro por búsqueda general
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('cedula', 'like', '%' . $search . '%')
                    // ->orWhere('funcion', 'like', '%' . $search . '%')
                    ->orWhere('apellidos_nombres', 'like', '%' . $search . '%')
                    ->orWhere('titulos', 'like', '%' . $search . '%')
                    ->orWhere('titulos_senescyt', 'like', '%' . $search . '%')
                    ->orWhere('capacitacion', 'like', '%' . $search . '%');
                    // ->orWhere('servicio_grupal', 'like', '%' . $search . '%');
            });
        }

        // Filtros de alertas y contrato_estudios
        if ($request->filled('alertas')) {
            if ($request->alertas === 'si') {
                $query->whereNotNull('alertas');
            } elseif ($request->alertas === 'no') {
                $query->whereNull('alertas');
            }
        }

        if ($request->filled('contrato_estudios')) {
            if ($request->contrato_estudios === 'si') {
                $query->whereNotNull('contrato_estudios');
            } elseif ($request->contrato_estudios === 'no') {
                $query->whereNull('contrato_estudios');
            }
        }

        // Filtros individuales
        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }

        if ($request->filled('hijos_menor_igual_18')) {
            $query->where('hijos_menor_igual_18', $request->hijos_menor_igual_18);
        }

        if ($request->filled('tipo_personal')) {
            $query->where('tipo_personal', $request->tipo_personal);
        }

        if ($request->filled('estado_civil')) {
            $query->where('estado_civil', $request->estado_civil);
        }

        if ($request->filled('nomenclatura_efectiva')) {
            $query->where('nomenclatura_efectiva', $request->nomenclatura_efectiva);
        }

        if ($request->filled('cdg_promocion')) {
            $query->where('cdg_promocion', $request->cdg_promocion);
        }

        if ($request->filled('grado') && is_array($request->grado)) {
            $query->whereIn('grado', $request->grado);
        }

        if ($request->filled('cuadro_policial')) {
            $query->where('cuadro_policial', $request->cuadro_policial);
        }

        if ($request->filled('provincia_trabaja')) {
            $query->where('provincia_trabaja', $request->provincia_trabaja);
        }

        if ($request->filled('provincia_vive')) {
            $query->where('provincia_vive', $request->provincia_vive);
        }

        // Filtros de fechas
        if ($request->filled('fecha_ingreso')) {
            $fechas = explode(" to ", $request->fecha_ingreso);
            if (count($fechas) == 2) {
                $query->whereBetween('fecha_ingreso', [$fechas[0], $fechas[1]]);
            }
        }

        if ($request->filled('fecha_pase_anterior')) {
            $fechas = explode(" to ", $request->fecha_pase_anterior);
            if (count($fechas) == 2) {
                $query->whereBetween('fecha_pase_anterior', [$fechas[0], $fechas[1]]);
            }
        }

        if ($request->filled('fecha_pase_actual')) {
            $fechas = explode(" to ", $request->fecha_pase_actual);
            if (count($fechas) == 2) {
                $query->whereBetween('fecha_pase_actual', [$fechas[0], $fechas[1]]);
            }
        }

        // Provincias para histórico
        $provincias_ecuador = [
            'AZUAY', 'BOLIVAR', 'CAÑAR', 'CARCHI', 'CHIMBORAZO', 'COTOPAXI', 'EL ORO',
            'ESMERALDAS', 'GALAPAGOS', 'GUAYAS', 'IMBABURA', 'LOJA', 'LOS RIOS', 'MANABI',
            'MORONA SANTIAGO', 'NAPO', 'ORELLANA', 'PASTAZA', 'PICHINCHA', 'SANTA ELENA',
            'STO DOMINGO TSACHILAS', 'SUCUMBIOS', 'TUNGURAHUA', 'ZAMORA CHINCHIPE'
        ];

        $historico = DB::table('usuarios')->pluck('historico_pases')->toArray();

        $provinciasDetectadas = [];

        foreach ($historico as $registro) {
            foreach ($provincias_ecuador as $provincia) {
                if (stripos($registro, $provincia) !== false) {
                    $provinciasDetectadas[] = $provincia;
                }
            }
        }

        $provinciasFiltradas = array_unique($provinciasDetectadas);
        sort($provinciasFiltradas);

        // Filtro por provincia encontrada en histórico
        if ($request->filled('provincia')) {
            $query->where('historico_pases', 'like', '%' . $request->provincia . '%');
        }

        // Listas desplegables
        $sexo = Usuario::select('sexo')->distinct()->pluck('sexo');
        $hijos_menor_igual_18 = Usuario::select('hijos_menor_igual_18')->distinct()->pluck('hijos_menor_igual_18');
        $estado_civil = Usuario::select('estado_civil')->distinct()->whereNotNull('estado_civil')->orderBy('estado_civil')->pluck('estado_civil');
        $nomenclatura_efectiva = Usuario::select('nomenclatura_efectiva')->distinct()->whereNotNull('nomenclatura_efectiva')->orderBy('nomenclatura_efectiva')->pluck('nomenclatura_efectiva');
        $cdg_promocion = Usuario::select('cdg_promocion')->distinct()->whereNotNull('cdg_promocion')->orderBy('cdg_promocion')->pluck('cdg_promocion');
        $grado = Usuario::select('grado')->distinct()->whereNotNull('grado')->pluck('grado');
        $cuadro_policial = Usuario::select('cuadro_policial')->distinct()->whereNotNull('cuadro_policial')->orderBy('cuadro_policial')->pluck('cuadro_policial');
        $provincia_trabaja = Usuario::select('provincia_trabaja')->distinct()->whereNotNull('provincia_trabaja')->orderBy('provincia_trabaja')->pluck('provincia_trabaja');
        $provincia_vive = Usuario::select('provincia_vive')->distinct()->whereNotNull('provincia_vive')->orderBy('provincia_vive')->pluck('provincia_vive');

        $usuarios = $query->paginate(50);

        return view('usuarios.index', compact(
            'usuarios',
            'estado_civil',
            'nomenclatura_efectiva',
            'cdg_promocion',
            'provincia_trabaja',
            'provincia_vive',
            'cuadro_policial',
            'grado',
            'hijos_menor_igual_18',
            'sexo',
            'provinciasFiltradas',
            'request'
        ));
    }

    public function show($id)
    {
        $usuario = Usuario::findOrFail($id);
        $meritosContados = [];


        if ($usuario->meritos) {
            $meritos = explode('|', $usuario->meritos);
            foreach ($meritos as $merito) {
                $meritoLimpio = trim($merito);
                if (!empty($meritoLimpio)) {
                    $meritosContados[$meritoLimpio] = ($meritosContados[$meritoLimpio] ?? 0) + 1;
                }
            }
        }





        return view('usuarios.show', compact('usuario', 'meritosContados'));
    }


    public function filtrarUsuarios(Request $request)
    {
        // Reutiliza la lógica de filtros que ya tienes en el método index
        $query = Usuario::query();

        // Filtro por búsqueda general
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('cedula', 'like', '%' . $search . '%')
                    ->orWhere('funcion', 'like', '%' . $search . '%')
                    ->orWhere('titulos', 'like', '%' . $search . '%')
                    ->orWhere('titulos_senescyt', 'like', '%' . $search . '%')
                    ->orWhere('capacitacion', 'like', '%' . $search . '%')
                    ->orWhere('servicio_grupal', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('alertas')) {
            if ($request->alertas === 'si') {
                $query->whereNotNull('alertas');
            } elseif ($request->alertas === 'no') {
                $query->whereNull('alertas');
            }
        }

        if ($request->filled('contrato_estudios')) {
            if ($request->contrato_estudios === 'si') {
                $query->whereNotNull('contrato_estudios');
            } elseif ($request->contrato_estudios === 'no') {
                $query->whereNull('contrato_estudios');
            }
        }


        // Filtros individuales
        if ($request->filled('sexo')) {
            $query->where('sexo', $request->sexo);
        }

        if ($request->filled('hijos_menor_igual_18')) {
            $query->where('hijos_menor_igual_18', $request->hijos_menor_igual_18);
        }

        if ($request->filled('tipo_personal')) {
            $query->where('tipo_personal', $request->tipo_personal);
        }

        if ($request->filled('estado_civil')) {
            $query->where('estado_civil', $request->estado_civil);
        }

        if ($request->filled('nomenclatura_efectiva')) {
            $query->where('nomenclatura_efectiva', $request->nomenclatura_efectiva);
        }

        if ($request->filled('cdg_promocion')) {
            $query->where('cdg_promocion', $request->cdg_promocion);
        }

        if ($request->filled('grado') && is_array($request->grado)) {
            $query->whereIn('grado', $request->grado);
        }

        if ($request->filled('cuadro_policial')) {
            $query->where('cuadro_policial', $request->cuadro_policial);
        }

        if ($request->filled('provincia_trabaja')) {
            $query->where('provincia_trabaja', $request->provincia_trabaja);
        }

        if ($request->filled('provincia_vive')) {
            $query->where('provincia_vive', $request->provincia_vive);
        }

        // Filtros de fechas
        if ($request->filled('fecha_ingreso')) {
            $fechas = explode(" to ", $request->fecha_ingreso);
            if (count($fechas) == 2) {
                $query->whereBetween('fecha_ingreso', [$fechas[0], $fechas[1]]);
            }
        }

        if ($request->filled('fecha_pase_anterior')) {
            $fechas = explode(" to ", $request->fecha_pase_anterior);
            if (count($fechas) == 2) {
                $query->whereBetween('fecha_pase_anterior', [$fechas[0], $fechas[1]]);
            }
        }

        if ($request->filled('fecha_pase_actual')) {
            $fechas = explode(" to ", $request->fecha_pase_actual);
            if (count($fechas) == 2) {
                $query->whereBetween('fecha_pase_actual', [$fechas[0], $fechas[1]]);
            }
        }

        // Provincias para histórico
        if ($request->filled('provincia')) {
            $query->where('historico_pases', 'like', '%' . $request->provincia . '%');
        }
        $usuarios = $query->get(); // Obtén los usuarios filtrados


        return $query->get(); // Devuelve todos los usuarios filtrados
    }


    public function export(Request $request, $type)
    {
        $usuarios = $this->filtrarUsuarios($request);

        if ($type === 'excel') {
            return Excel::download(new UsuariosExport($usuarios), 'usuarios.xlsx');
        }

        if ($type === 'pdf') {
            return Pdf::loadView('exports.usuarios_pdf', compact('usuarios'))
                      ->download('usuarios.pdf');
        }

        abort(404);
    }


        public function agregar($id)
    {
        $usuario = \App\Models\Usuario::findOrFail($id);
        $seleccionados = session()->get('usuarios_seleccionados', []);

        if (in_array($id, $seleccionados)) {
            return redirect()->back()->with('error', 'ERROR, USUARIO YA SELECCIONADO');
        }

        $seleccionados[] = $id;
        session()->put('usuarios_seleccionados', $seleccionados);

        return redirect()->back()->with('success', 'SERVIDOR POLICIAL AGREGADO CORRECTAMENTE');
    }


    public function seleccionados()
    {
        $ids = session()->get('usuarios_seleccionados', []);
        $usuarios = Usuario::whereIn('id', $ids)->get();

        // Extraemos valores únicos para filtros
        $filtros = [
            'estado_civil' => Usuario::distinct()->pluck('estado_civil')->filter()->unique()->sort()->values(),
            'provincia_trabaja' => Usuario::distinct()->pluck('provincia_trabaja')->filter()->unique()->sort()->values(),
            'provincia_vive' => Usuario::distinct()->pluck('provincia_vive')->filter()->unique()->sort()->values(),
            'contrato_estudios' => Usuario::distinct()->pluck('contrato_estudios')->filter()->unique()->sort()->values(),
            'conyuge_policia_activo' => Usuario::distinct()->pluck('conyuge_policia_activo')->filter()->unique()->sort()->values(),
            'enf_catast_sp' => Usuario::distinct()->pluck('enf_catast_sp')->filter()->unique()->sort()->values(),
            'enf_catast_conyuge_hijos' => Usuario::distinct()->pluck('enf_catast_conyuge_hijos')->filter()->unique()->sort()->values(),
            'discapacidad_sp' => Usuario::distinct()->pluck('discapacidad_sp')->filter()->unique()->sort()->values(),
            'discapacidad_conyuge_hijos' => Usuario::distinct()->pluck('discapacidad_conyuge_hijos')->filter()->unique()->sort()->values(),
            'hijos_menor_igual_18' => Usuario::distinct()->pluck('hijos_menor_igual_18')->filter()->unique()->sort()->values(),
            'alertas' => Usuario::distinct()->pluck('alertas')->filter()->unique()->sort()->values(),
            'novedad_situacion' => Usuario::distinct()->pluck('novedad_situacion')->filter()->unique()->sort()->values(),
            'maternidad' => Usuario::distinct()->pluck('maternidad')->filter()->unique()->sort()->values(),
        ];

        return view('usuarios.seleccionados', compact('usuarios', 'filtros'));
    }

    public function eliminarSeleccionado($id)
    {
        $seleccionados = session()->get('usuarios_seleccionados', []);
        $seleccionados = array_filter($seleccionados, fn($uid) => $uid != $id);
        session()->put('usuarios_seleccionados', $seleccionados);

        return redirect()->back()->with('success', 'Usuario eliminado de la selección.');
    }


    public function factibilidad(Request $request)
    {
        $ids = session()->get('usuarios_seleccionados', []);
        $usuarios = Usuario::whereIn('id', $ids)->get();

        // Campos binarios que se analizan con "sí" o "no"
        $camposBinarios = [
            'contrato_estudios',
            'conyuge_policia_activo',
            'enf_catast_sp',
            'enf_catast_conyuge_hijos',
            'discapacidad_sp',
            'discapacidad_conyuge_hijos',
            'hijos_menor_igual_18',
            'alertas',
            'novedad_situacion',
            'maternidad'
        ];

        // Todos los filtros posibles
        $todosFiltros = array_merge(['estado_civil', 'provincia_trabaja', 'provincia_vive'], $camposBinarios);

        // Filtrar solo los parámetros realmente seleccionados por el usuario
        $filtrosActivos = collect($todosFiltros)->filter(fn($campo) => $request->filled($campo))->values();

        $totalCriterios = $filtrosActivos->count();

        $usuarios = $usuarios->map(function ($usuario) use ($request, $filtrosActivos, $camposBinarios, $totalCriterios) {
            if ($totalCriterios === 0) {
                $usuario->factibilidad = 0;
                return $usuario;
            }

            $score = 0;

            foreach ($filtrosActivos as $campo) {
                $valorUsuario = trim(strtolower((string) $usuario->$campo));
                $valorFiltro = trim(strtolower($request->$campo));

                if (in_array($campo, $camposBinarios)) {
                    // Campo binario
                    if ($valorFiltro === 'si' && $valorUsuario !== '') {
                        $score++;
                    } elseif ($valorFiltro === 'no' && $valorUsuario === '') {
                        $score++;
                    }
                } else {
                    // Campo de coincidencia exacta (texto)
                    if ($valorFiltro === $valorUsuario) {
                        $score++;
                    }
                }
            }

            $usuario->factibilidad = round(($score / $totalCriterios) * 100);
            return $usuario;
        });

        return view('usuarios.factibilidad_resultado', compact('usuarios'));
    }

    public function exportarFactibilidadPdf(Request $request)
    {
        $ids = session()->get('usuarios_seleccionados', []);
        $usuarios = \App\Models\Usuario::whereIn('id', $ids)->get();
        $user = auth()->user(); // usuario autenticado

        $campos = [
            'estado_civil', 'provincia_trabaja', 'provincia_vive',
            'contrato_estudios', 'conyuge_policia_activo', 'enf_catast_sp',
            'enf_catast_conyuge_hijos', 'discapacidad_sp', 'discapacidad_conyuge_hijos',
            'hijos_menor_igual_18', 'alertas', 'novedad_situacion', 'maternidad'
        ];

        $filtrosActivos = collect($campos)->filter(fn($campo) => $request->filled($campo))->values();
        $total = $filtrosActivos->count();

        $camposBinarios = array_slice($campos, 3);

        $usuarios = $usuarios->map(function ($usuario) use ($request, $filtrosActivos, $camposBinarios, $total) {
            $detalle = [];
            $score = 0;

            foreach ($filtrosActivos as $campo) {
                $valorUsuario = trim(strtolower((string) $usuario->$campo));
                $valorFiltro = trim(strtolower($request->$campo));

                $cumple = in_array($campo, $camposBinarios)
                    ? ($valorFiltro === 'si' && $valorUsuario !== '') || ($valorFiltro === 'no' && $valorUsuario === '')
                    : ($valorUsuario === $valorFiltro);

                if ($cumple) $score++;

                $detalle[] = [
                    'campo' => ucwords(str_replace('_', ' ', $campo)),
                    'esperado' => $valorFiltro,
                    'usuario' => $valorUsuario ?: '---',
                    'cumple' => $cumple
                ];
            }

            $usuario->factibilidad = $total ? round(($score / $total) * 100) : 0;
            $usuario->detalle = $detalle;
            return $usuario;
        });

        $fecha = now()->format('d/m/Y H:i');

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('usuarios.factibilidad_pdf', compact('usuarios', 'user', 'fecha'))
            ->download('reporte_factibilidad.pdf');
    }

// ==== Vista de carga ====
    public function opciones()
    {
        return view('usuarios.opciones');
    }

    // ==== Procesa Excel y guarda universo de cédulas en sesión ====
    public function masivo(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv'
        ]);

        $archivo = $request->file('archivo');
        $spreadsheet = IOFactory::load($archivo->getRealPath());
        $hoja = $spreadsheet->getActiveSheet();
        $filas = $hoja->toArray(null, true, true, true);

        // Normalizador de cédulas: solo dígitos y pad a 10
        $norm = function (?string $v): ?string {
            if ($v === null) return null;
            $d = preg_replace('/\D+/', '', (string)$v);
            if ($d === '') return null;
            // Ajusta a 10; si tu sistema usa 9 u 11, cambia aquí
            return str_pad(substr($d, -10), 10, '0', STR_PAD_LEFT);
        };

        $cedulas = [];
        $esPrimera = true;
        foreach ($filas as $fila) {
            // Si la primera fila es encabezado, sáltala (detecta no-dígitos)
            if ($esPrimera) {
                $esPrimera = false;
                $posible = $fila['A'] ?? '';
                $soloDig = preg_replace('/\D+/', '', (string)$posible);
                if ($soloDig === '' || strlen($soloDig) < 8) {
                    // parece encabezado, no lo tomo
                    continue;
                }
            }
            $valor = $norm($fila['A'] ?? null);
            if ($valor !== null) {
                $cedulas[] = $valor;
            }
        }

        $cedulas = collect($cedulas)->filter()->unique()->values()->all();

        if (empty($cedulas)) {
            return back()->with('error', 'No se encontraron cédulas válidas en el archivo.');
        }

        // Guarda en sesión
        $request->session()->put('cedulas_filtradas', $cedulas);

        // Redirige a resultados
        return redirect()->route('usuarios.resultados');
    }

    // ==== Listado con filtros (siempre restringido por universo) ====
    public function resultado(Request $request)
    {
        // Botón "Limpiar" => borra universo
        if ($request->boolean('clear')) {
            $request->session()->forget('cedulas_filtradas');
            return redirect()->route('usuarios.resultados');
        }

        // --- Parámetros UI ---
        $alertasSeleccionadas = (array) $request->input('alertas', []);
        $estado               = $request->input('estado', 'todos');
        $q                    = trim((string) $request->input('q', ''));

        // --- Lista blanca de columnas de alerta ---
        $opcionesAlertas = [
            'contrato_estudios',
            'enf_catast_sp',
            'enf_catast_conyuge_hijos',
            'discapacidad_sp',
            'discapacidad_conyuge_hijos',
            'alertas',
            'alertas_problemas_salud',
            'novedad_situacion',
        ];
        $alertasSeleccionadas = array_values(array_intersect($alertasSeleccionadas, $opcionesAlertas));

        // --- Normalizador de cédulas (mismo de masivo) ---
        $norm = function ($v) {
            $d = preg_replace('/\D+/', '', (string)$v);
            if ($d === '') return null;
            return str_pad(substr($d, -10), 10, '0', STR_PAD_LEFT);
        };

        // --- Universo de cédulas: primero query (hidden), si no, sesión ---
        $cedulas = [];
        $param = $request->input('cedulas', null);
        if (is_string($param) && $param !== '') {
            foreach (explode(',', $param) as $c) {
                $n = $norm($c);
                if ($n !== null) $cedulas[] = $n;
            }
        } elseif (is_array($param)) {
            foreach ($param as $c) {
                $n = $norm($c);
                if ($n !== null) $cedulas[] = $n;
            }
        }

        if (empty($cedulas)) {
            // Sesión
            $fromSession = (array) $request->session()->get('cedulas_filtradas', []);
            foreach ($fromSession as $c) {
                $n = $norm($c);
                if ($n !== null) $cedulas[] = $n;
            }
        }

        // --- Base de consulta ---
        $base = DB::table('usuarios');

        // ** GARANTÍA: si NO hay universo, NO mostrar nada **
        if (empty($cedulas)) {
            $base->whereRaw('1=0'); // nunca devuelve filas
        } else {
            $base->whereIn('cedula', $cedulas);
        }

        // Búsqueda libre
        if ($q !== '') {
            $p = "%{$q}%";
            $base->where(function ($qq) use ($p) {
                $qq->where('cedula', 'like', $p)
                    ->orWhere('apellidos_nombres', 'like', $p)
                    ->orWhere('grado', 'like', $p)
                    ->orWhere('provincia_trabaja', 'like', $p);
            });
        }

        // Helper de "valor significativo" en columnas de alerta
        $colTieneValor = function($q, $col) {
            $q->whereNotNull($col)
                ->whereRaw("TRIM(`$col`) <> ''")
                ->whereRaw("UPPER(TRIM(`$col`)) NOT IN ('NO','N/A','NA')")
                ->whereRaw("TRIM(CAST(`$col` AS CHAR)) <> '0'");
        };

        // Filtro por alertas
        if (!empty($alertasSeleccionadas)) {
            $base->where(function ($q2) use ($alertasSeleccionadas, $colTieneValor) {
                foreach ($alertasSeleccionadas as $col) {
                    $q2->orWhere(function ($c) use ($col, $colTieneValor) {
                        $colTieneValor($c, $col);
                    });
                }
            });
        } elseif ($estado === 'alerta') {
            $base->where(function ($q2) use ($opcionesAlertas, $colTieneValor) {
                foreach ($opcionesAlertas as $col) {
                    $q2->orWhere(function ($c) use ($col, $colTieneValor) {
                        $colTieneValor($c, $col);
                    });
                }
            });
        }

        // Estado activo
        if ($estado === 'activo') {
            $base->where(function ($q3) {
                $q3->whereNull('novedad_situacion')
                    ->orWhere('novedad_situacion', '')
                    ->orWhereRaw("UPPER(TRIM(`novedad_situacion`)) = 'ACTIVO'");
            });
        }

        // Columnas a mostrar
        $selectCols = [
            'cedula', 'apellidos_nombres', 'grado', 'provincia_trabaja',
            'estado_civil', 'promocion', 'novedad_situacion',
            'contrato_estudios', 'enf_catast_sp', 'enf_catast_conyuge_hijos',
            'discapacidad_sp', 'discapacidad_conyuge_hijos', 'alertas',
            'alertas_problemas_salud','alerta_devengacion',
            'alerta_marco_legal',
            'observacion_tenencia',
            'pase_ucp_ccp_cpl',
            'FaseMaternidadUDGA',
            'fase_maternidad',
            'maternidad',
        ];

        // Paginación (preserva filtros + universo)
        $usuarios = $base->select($selectCols)
            ->orderBy('apellidos_nombres')
            ->paginate(100)
            ->appends(array_merge(
                $request->query(),
                !empty($cedulas) ? ['cedulas' => implode(',', $cedulas)] : []
            ));

        return view('usuarios.resultado', [
            'usuarios'             => $usuarios,
            'opcionesAlertas'      => $opcionesAlertas,
            'alertasSeleccionadas' => $alertasSeleccionadas,
            'estadoSeleccionado'   => $estado,
            'q'                    => $q,
            'cedulas'              => $cedulas, // para el hidden
        ]);
    }


}
