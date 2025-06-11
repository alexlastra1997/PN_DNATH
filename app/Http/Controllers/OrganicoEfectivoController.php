<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Cargo;

class OrganicoEfectivoController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::select('cedula', 'grado', 'apellidos_nombres', 'nomenclatura_territorio_efectivo');

        if ($request->filled('grado')) {
            $query->where('grado', $request->grado);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('cedula', 'like', "%{$buscar}%")
                ->orWhere('apellidos_nombres', 'like', "%{$buscar}%");
            });
        }

        $usuarios = $query->paginate(50)->appends($request->all());

        // Para llenar el select dinámico con todos los grados únicos
        $grados = Usuario::select('grado')->distinct()->orderBy('grado')->pluck('grado');

        return view('organico_efectivo', compact('usuarios', 'grados'));
    }

    public function agregar(Request $request)
    {
        $cedula = $request->input('cedula');

        $usuario = Usuario::where('cedula', $cedula)->first();
        if (!$usuario) return redirect()->back()->with('error', 'Usuario no encontrado');

        $carrito = session()->get('carrito_usuarios', []);
        
        // Evitar duplicados
        if (!array_key_exists($cedula, $carrito)) {
            $carrito[$cedula] = $usuario;
            session(['carrito_usuarios' => $carrito]);
        }

        return redirect()->route('organico.efectivo')->with('success', 'Usuario agregado');
    }

    public function mostrarSeleccionados()
{
    $usuariosSeleccionados = session('carrito_usuarios', []);
    $cargos = \App\Models\Cargo::select('numero', 'cargo')->orderBy('numero')->get();

    return view('organico_efectivo_show', compact('usuariosSeleccionados', 'cargos'));
}

    public function limpiarCarrito()
    {
        session()->forget('carrito_usuarios');
        return redirect()->route('organico.efectivo')->with('success', 'Lista vaciada');
    }

    public function buscarVacante(Request $request)
    {
        $cargo = $request->input('cargo');
        $tiempo = $request->input('tiempo');

        $query = \App\Models\Usuario::where('funcion', 'like', "%{$cargo}%");

        if ($tiempo) {
            $hoy = Carbon::now();
            switch ($tiempo) {
                case '2':
                    $fechaMinima = $hoy->copy()->subYears(2);
                    break;
                case '3':
                    $fechaMinima = $hoy->copy()->subYears(3);
                    break;
                case '4':
                    $fechaMinima = $hoy->copy()->subYears(4);
                    break;
                case '5+':
                    $fechaMinima = $hoy->copy()->subYears(5);
                    break;
                default:
                    $fechaMinima = null;
            }

            if ($fechaMinima) {
                $query->whereDate('fecha_territorio_efectivo', '<=', $fechaMinima);
            }
        }

        $usuariosCoincidentes = $query->paginate(20)->appends($request->all());

        $cargos = \App\Models\Cargo::select('numero', 'cargo')->orderBy('numero')->get();

        return view('organico_efectivo_show', [
            'usuariosSeleccionados' => session('carrito_usuarios', []),
            'usuariosCoincidentes' => $usuariosCoincidentes,
            'cargos' => $cargos,
            'cargoSeleccionado' => $cargo,
            'tiempoSeleccionado' => $tiempo,
        ]);
    }

public function evaluar(Request $request)
{
    $cedulaB = $request->input('cedula_b');
    $usuarioA = collect(session('carrito_usuarios', []))->first();
    $usuarioB = Usuario::where('cedula', $cedulaB)->first();

    if (!$usuarioA || !$usuarioB) {
        return back()->with('error', 'No se encontró usuario A o B');
    }

    $cargo = Cargo::whereRaw('LOWER(cargo) = ?', [strtolower($usuarioB->funcion)])->first();

    $cargoSugerido = null;
    if (!$cargo) {
        $cargosTodos = Cargo::all();
        $mejorCoincidencia = 0;
        foreach ($cargosTodos as $c) {
            similar_text(strtolower($c->cargo), strtolower($usuarioB->funcion), $porcentaje);
            if ($porcentaje > $mejorCoincidencia) {
                $mejorCoincidencia = $porcentaje;
                $cargo = $c;
                $cargoSugerido = true;
            }
        }
    }

    $gradosJerarquia = [
        'SBOS', 'SGOS', 'SGOP', 'SGOM', 'SGT', 'SGT1', 'SGT2', 'SBO', 'SBT', 'TNT', 'STNT', 'ASP',
        'SUBS', 'SUBT', 'TTE', 'CPT', 'MYR', 'TCNL', 'CRNL', 'GEN'
    ];

    $gradoA = strtoupper(trim($usuarioA['grado']));
    $gradoB = strtoupper(trim($usuarioB->grado));
    $gradoMin = strtoupper(trim($cargo->directivo_minimo ?? $cargo->tecnico_minimo));
    $gradoMax = strtoupper(trim($cargo->directivo_maximo ?? $cargo->tecnico_maximo));

    $indexA = array_search($gradoA, $gradosJerarquia);
    $indexB = array_search($gradoB, $gradosJerarquia);
    $indexMin = array_search($gradoMin, $gradosJerarquia);
    $indexMax = array_search($gradoMax, $gradosJerarquia);

    $resultado = 'NO FACTIBLE';
    if ($indexA !== false && $indexMin !== false && $indexMax !== false) {
        if ($indexA < $indexMin) {
            $mensaje = "⚠️ El servidor policial <strong>{$usuarioA['apellidos_nombres']}</strong> está por DEBAJO del mínimo requerido.";
        } elseif ($indexA > $indexMax) {
            $mensaje = "❌ El servidor policial <strong>{$usuarioA['apellidos_nombres']}</strong> está por ENCIMA del máximo permitido.";
        } else {
            $mensaje = "✅ El servidor policial <strong>{$usuarioA['apellidos_nombres']}</strong> cumple con el perfil de grado requerido.";
            $resultado = 'FACTIBLE';
        }
    } else {
        $mensaje = "❌ No se pudo comparar porque uno de los grados no está definido en la jerarquía.";
    }

    $mensajeB = null;
    $validezB = 'FACTIBLE';
    if ($indexB === false || $indexMin === false || $indexMax === false) {
        $mensajeB = "❌El servidor policial <strong>{$usuarioB->apellidos_nombres}</strong> no está definido correctamente.";
        $validezB = 'NO FACTIBLE';
    } elseif ($indexB < $indexMin) {
        $mensajeB = "❌ El servidor policial <strong>{$usuarioB->apellidos_nombres}</strong> está por DEBAJO del grado mínimo requerido para su propio cargo.";
        $validezB = 'NO FACTIBLE';
    } elseif ($indexB > $indexMax) {
        $mensajeB = "❌ El servidor policial <strong>{$usuarioB->apellidos_nombres}</strong> está por ENCIMA del grado máximo permitido para su propio cargo.";
        $validezB = 'NO FACTIBLE';
    } else {
        $mensajeB = "✅ El servidor policial <strong>{$usuarioB->apellidos_nombres}</strong> cumple con el perfil de grado para su cargo.";
    }

    return view('evaluacion_usuario', compact(
        'usuarioA', 'usuarioB', 'cargo',
        'mensaje', 'resultado',
        'mensajeB', 'validezB',
        'cargoSugerido'
    ));
}



}
