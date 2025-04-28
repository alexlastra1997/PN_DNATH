<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $cantidadCedulasUnicas = DB::table('usuarios')
        ->whereNotNull('cedula')
        ->distinct()
        ->count('cedula');

        $cuadroClases = Usuario::where('cuadro_policial', 'Clases y Policías')->count();
        $cuadroSubalternos = Usuario::where('cuadro_policial', 'Oficiales Subalternos')->count();
        $cuadroSuperiores = Usuario::where('cuadro_policial', 'Oficiales Superiores')->count();

        // Nueva data para gráfico de provincias
        $provinciasData = Usuario::select('provincia_trabaja', DB::raw('count(*) as total'))
        ->groupBy('provincia_trabaja')
        ->pluck('total', 'provincia_trabaja');

    return view('dashboard', compact('cantidadCedulasUnicas','cuadroClases','cuadroSubalternos','cuadroSuperiores','provinciasData'));
    }
}
