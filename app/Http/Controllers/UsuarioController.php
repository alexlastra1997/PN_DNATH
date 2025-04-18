<?php

// app/Http/Controllers/UsuarioController.php


namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Exports\UsuariosExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\UusuariosExport;
use Illuminate\Support\Facades\DB;



class UsuarioController extends Controller
{
    public function index(Request $request)
    {

        $cantidadCedulasUnicas = DB::table('usuarios')
        ->whereNotNull('cedula')
        ->distinct()
        ->count('cedula');

        $cuadroClases = Usuario::where('cuadro_policial', 'Clases y Policías')->count();
        $cuadroSubalternos = Usuario::where('cuadro_policial', 'Oficiales Subalternos')->count();
        $cuadroSuperiores = Usuario::where('cuadro_policial', 'Oficiales Superiores')->count();

        $query = Usuario::query();

        // Filtro por cédula
        if ($request->filled('search')) {
            $query->where('cedula', 'like', '%' . $request->search . '%');
        }

        // Filtro por sexo (H o M)
        if ($request->filled('sexo')) {
            $query->whereIn('sexo', $request->sexo);
        }

        // Filtro por número de hijos (cambiar 'hijos' a 'hijos18')
        if ($request->filled('hijos')) {
            $query->whereIn('hijos18', $request->hijos); // Cambié 'hijos' por 'hijos18'
        }

        // Paginar resultados
        $usuarios = $query->paginate(100);

        return view('usuarios.index', compact(
            'usuarios',
            'cantidadCedulasUnicas',
            'cuadroClases',
            'cuadroSubalternos',
            'cuadroSuperiores'
        ));
    }

    public function show(Usuario $usuario)
    {
        return view('usuarios.show', compact('usuario'));
    }

    // Método para exportar a PDF
    public function exportPDF(Request $request)
    {
        // Aplicar los filtros antes de generar el PDF
        $query = Usuario::query();

        if ($request->filled('search')) {
            $query->where('cedula', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('sexo')) {
            $query->whereIn('sexo', $request->sexo);
        }

        if ($request->filled('hijos')) {
            $query->whereIn('hijos18', $request->hijos);
        }

        $usuarios = $query->limit(500)->get(); // solo 500 resultados

        // Cargar la vista para el PDF
        $pdf = Pdf::loadView('usuarios.pdf', compact('usuarios'));

        // Descargar el PDF
        return $pdf->download('usuarios_filtrados.pdf');
    }

    // Método para exportar a Excel
    public function exportExcel(Request $request)
    
    {
        return Excel::download(new UsuariosExport($request), 'archivo.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
