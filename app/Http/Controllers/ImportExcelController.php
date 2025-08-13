<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsuariosImport;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class ImportExcelController extends Controller
{
    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls'
        ]);

           // Ajuste para evitar errores de tiempo y memoria
            ini_set('max_execution_time', 0); // sin límite de tiempo
            ini_set('memory_limit', '-1');    // sin límite de memoria

        Excel::import(new UsuariosImport, $request->file('archivo'));

        return back()->with('success', 'Datos importados correctamente.');
    }

    public function showForm()
{
    return view('importar_excel'); // Asegúrate de tener una vista con ese nombre en resources/views
}

public function eliminarTodos()
{
    
    Usuario::truncate(); // Vacía toda la tabla 'usuarios'

    return redirect()->back()->with('delete', 'Todos los usuarios fueron eliminados correctamente.');
}

public function showContra()
{
    return view('contra');
}

public function importContra(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls',
    ]);

    $collection = Excel::toCollection(null, $request->file('file'))->first();

    $cedulas = $collection->pluck(0)->map(function ($cedula) {
        return str_pad(trim($cedula), 10, '0', STR_PAD_LEFT); // asegura 10 dígitos
    })->toArray();

    // ✅ SOLO actualiza si no hay texto en alerta_contra
    foreach ($cedulas as $cedula) {
        DB::table('usuarios')
            ->where('cedula', $cedula)
            ->whereNull('alerta_contra') // ← solo si está vacío
            ->update(['alerta_contra' => '⚠️']);
    }

    // Obtiene los usuarios marcados
    $usuarios = DB::table('usuarios')
        ->whereIn('cedula', $cedulas)
        ->get();

    return redirect()->route('contra.view')->with('usuarios', $usuarios);
}

public function guardarNovedad(Request $request)
{
    $request->validate([
        'cedulas' => 'required',
         'novedad' => 'nullable|string|max:1000', // ← permite vacío
    ]);

    $cedulas = explode(',', $request->cedulas);
    $novedad = $request->novedad;

    DB::table('usuarios')
        ->whereIn('cedula', $cedulas)
        ->update(['alerta_contra' => $novedad]);

    return redirect()->route('contra.view')->with('success', '¡Novedad guardada con éxito!');
}

}
