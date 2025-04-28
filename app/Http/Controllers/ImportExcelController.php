<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsuariosImport;
use App\Models\Usuario;

class ImportExcelController extends Controller
{
    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls'
        ]);

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
}
