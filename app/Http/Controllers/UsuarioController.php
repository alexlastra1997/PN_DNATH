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

        $usuarios = $query->paginate(100);

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

  public function opciones()
    {
        return view('usuarios.opciones');
    }

    public function masivo(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls'
        ]);

        $archivo = $request->file('archivo');
        $spreadsheet = IOFactory::load($archivo);
        $hoja = $spreadsheet->getActiveSheet();
        $filas = $hoja->toArray();

        $cedulas = [];
        foreach($filas as $fila) {
            if (!empty($fila[0])) {
                $cedulas[] = trim($fila[0]);
            }
        }

        $usuarios = Usuario::whereIn('cedula', $cedulas)->get();

        $estados_civiles = Usuario::select('estado_civil')->distinct()->pluck('estado_civil');
        $promociones = Usuario::select('promocion')->distinct()->pluck('promocion');

        return view('usuarios.resultado', compact('usuarios', 'estados_civiles', 'promociones'));
    }

    public function filtrarProvinciaAjax(Request $request)
    {
        $cedulas = explode(',', $request->input('cedulas_filtradas'));
        $priorizados = ['PASCUALES', 'NUEVA PROSPERINA', 'DURAN', 'MACHALA', 'MANTA', 'BABAHOYO', 'SUR DMG',
            'PORTOVIEJO', 'DAULE', 'PORTETE', 'ESTEROS', 'QUEVEDO', 'PASAJE', '9 DE OCTUBRE',
            'LIBERTAD SALINAS', 'ESMERALDAS', 'MODELO', 'FLORIDA', 'EUGENIO ESPEJO', 'NARANJAL BALAO',
            'BUENA FE', 'HUAQUILLAS', 'ELOY ALFARO', 'LA DELICIA', 'PUEBLOVIEJO', 'VINCES',
            'LAGO AGRIO', 'PLAYAS', 'STA ELENA', 'QUITUMBE'];

        $query = Usuario::whereIn('cedula', $cedulas);
        $original = Usuario::whereIn('cedula', $cedulas)->get();

        if ($request->filled('provincia_trabaja')) $query->where('provincia_trabaja', $request->provincia_trabaja);
        if ($request->input('alerta') === 'SI') $query->whereNotNull('alertas');
        elseif ($request->input('alerta') === 'NO') $query->whereNull('alertas');

        $filtrosBooleanos = [
            'contrato_estudios', 'conyuge_policia_activo', 'enf_catast_sp',
            'enf_catast_conyuge_hijos', 'discapacidad_sp', 'discapacidad_conyuge_hijos'
        ];

        foreach ($filtrosBooleanos as $campo) {
            if ($request->input($campo) === 'SI') {
                $query->whereNotNull($campo);
            } elseif ($request->input($campo) === 'NO') {
                $query->whereNull($campo);
            }
        }

        if ($request->filled('estado_civil')) $query->where('estado_civil', $request->estado_civil);
        if (!empty($request->promocion)) $query->whereIn('promocion', $request->promocion);

        if ($request->input('sitio_priorizado') === 'SI') {
            $query->where(function ($q) use ($priorizados) {
                foreach ($priorizados as $palabra) {
                    $q->orWhere('nomenclatura_territorio_efectivo', 'like', "%{$palabra}%");
                }
            });
        } elseif ($request->input('sitio_priorizado') === 'NO') {
            $query->where(function ($q) use ($priorizados) {
                foreach ($priorizados as $palabra) {
                    $q->where('nomenclatura_territorio_efectivo', 'not like', "%{$palabra}%");
                }
            });
        }

        $aptos = $query->get();

        $no_aptos = $original->filter(fn($u) => !$aptos->contains('cedula', $u->cedula));

        return response()->json([
            'aptos' => view('usuarios.partials.tabla_aptos', compact('aptos'))->render(),
            'no_aptos' => view('usuarios.partials.tabla_no_aptos', compact('no_aptos'))->render()
        ]);
    }


public function descargarExcel(Request $request)
{
    $cedulas = explode(',', $request->input('cedulas'));

    $usuarios = \App\Models\Usuario::whereIn('cedula', $cedulas)
        ->select('cedula', 'grado', 'apellidos_nombres','nomenclatura_territorio_efectivo', 'descFuncion_territorio_efectivo', 'idDgpUnidad_territorio_efectivo', 'idDgpFuncion_territorio_efectivo')
        ->get();

    $exportData = $usuarios->map(function($u) {
        return [
            'CÉDULA' => $u->cedula,
            'GRADO' => $u->grado,
            'NOMBRE' => $u->apellidos_nombres,

            'NOMENCLATURA' => $u->nomenclatura_territorio_efectivo,
            'FUNCION' => $u->descFuncion_territorio_efectivo,
            'ID UNIDAD' => $u->idDgpUnidad_territorio_efectivo,
            'ID FUNCION' => $u->idDgpFuncion_territorio_efectivo,
            
        ];
    });

    $filename = 'traslado_masivo_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(new class($exportData) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
        protected $data;
        public function __construct($data) { $this->data = $data; }
        public function collection() { return collect($this->data); }
        public function headings(): array { return ['CÉDULA', 'GRADO', 'NOMBRE','NOMENCLATURA',
            'FUNCION',
            'ID UNIDAD',
            'ID FUNCION' ]; }
    }, $filename);
}

   
}
