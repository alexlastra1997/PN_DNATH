<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\CargoImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Cargo;


class CargoController extends Controller
{
    public function index()
    {
        return view('cargos.import');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new CargoImport, $request->file('archivo'));

        return redirect()->back()->with('success', 'Importación completada.');
    }

    public function eliminarTodo()
    {
        Cargo::truncate(); // Borra todos los registros de la tabla cargos

        return redirect()->back()->with('delete', 'Todos los registros fueron eliminados.');
    }

}

