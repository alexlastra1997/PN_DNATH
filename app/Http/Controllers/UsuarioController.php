<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\NumDemerito;
use Illuminate\Http\Request;
use App\Exports\UsuariosExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;
use App\Models\Alerta;


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
                    ->orWhere('funcion', 'like', '%' . $search . '%')
                    ->orWhere('apellidos_nombres', 'like', '%' . $search . '%')
                    ->orWhere('titulos', 'like', '%' . $search . '%')
                    ->orWhere('titulos_senescyt', 'like', '%' . $search . '%')
                    ->orWhere('capacitacion', 'like', '%' . $search . '%')
                    ->orWhere('servicio_grupal', 'like', '%' . $search . '%');
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

        if ($request->filled('hijos18')) {
            $query->where('hijos18', $request->hijos18);
        }

        if ($request->filled('tipo_personal')) {
            $query->where('tipo_personal', $request->tipo_personal);
        }

        if ($request->filled('estado_civil')) {
            $query->where('estado_civil', $request->estado_civil);
        }

        if ($request->filled('unidad')) {
            $query->where('unidad', $request->unidad);
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
            'ESMERALDAS', 'GALÁPAGOS', 'GUAYAS', 'IMBABURA', 'LOJA', 'LOS RÍOS', 'MANABÍ',
            'MORONA SANTIAGO', 'NAPO', 'ORELLANA', 'PASTAZA', 'PICHINCHA', 'SANTA ELENA',
            'SANTO DOMINGO DE LOS TSÁCHILAS', 'SUCUMBÍOS', 'TUNGURAHUA', 'ZAMORA CHINCHIPE'
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
        $hijos18 = Usuario::select('hijos18')->distinct()->pluck('hijos18');
        $estado_civil = Usuario::select('estado_civil')->distinct()->whereNotNull('estado_civil')->orderBy('estado_civil')->pluck('estado_civil');
        $unidad = Usuario::select('unidad')->distinct()->whereNotNull('unidad')->orderBy('unidad')->pluck('unidad');
        $cdg_promocion = Usuario::select('cdg_promocion')->distinct()->whereNotNull('cdg_promocion')->orderBy('cdg_promocion')->pluck('cdg_promocion');
        $grado = Usuario::select('grado')->distinct()->whereNotNull('grado')->pluck('grado');
        $cuadro_policial = Usuario::select('cuadro_policial')->distinct()->whereNotNull('cuadro_policial')->orderBy('cuadro_policial')->pluck('cuadro_policial');
        $provincia_trabaja = Usuario::select('provincia_trabaja')->distinct()->whereNotNull('provincia_trabaja')->orderBy('provincia_trabaja')->pluck('provincia_trabaja');
        $provincia_vive = Usuario::select('provincia_vive')->distinct()->whereNotNull('provincia_vive')->orderBy('provincia_vive')->pluck('provincia_vive');

        $usuarios = $query->paginate(100);

        return view('usuarios.index', compact(
            'usuarios',
            'estado_civil',
            'unidad',
            'cdg_promocion',
            'provincia_trabaja',
            'provincia_vive',
            'cuadro_policial',
            'grado',
            'hijos18',
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

        if ($request->filled('hijos18')) {
            $query->where('hijos18', $request->hijos18);
        }

        if ($request->filled('tipo_personal')) {
            $query->where('tipo_personal', $request->tipo_personal);
        }

        if ($request->filled('estado_civil')) {
            $query->where('estado_civil', $request->estado_civil);
        }

        if ($request->filled('unidad')) {
            $query->where('unidad', $request->unidad);
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
}
