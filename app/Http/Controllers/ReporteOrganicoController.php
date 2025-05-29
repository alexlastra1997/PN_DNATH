<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ReporteOrganicoImport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteOrganicoController extends Controller
{
    public function showForm()
    {
        return view('reporte.importar');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ReporteOrganicoImport, $request->file('archivo'));

        return back()->with('success', 'Importación exitosa');
    }
}

