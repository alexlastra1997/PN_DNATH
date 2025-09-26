<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsuariosImport;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class ImportExcelController extends Controller
{
    public function showForm()
    {
        return view('importar_excel');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls'
        ]);

        // Evitar límites en archivos grandes
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $import = new UsuariosImport();
        Excel::import($import, $request->file('archivo'));

        $resumen = $import->resumen();

        return back()->with([
            'success' => 'Datos importados correctamente.',
            'resumen' => $resumen,
        ]);
    }

    public function eliminarTodos()
    {
        Usuario::truncate();
        return redirect()->back()->with('delete', 'Todos los usuarios fueron eliminados correctamente.');
    }

    // --- Opcionales, si ya los usas en tu app ---

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
            return str_pad(trim($cedula), 10, '0', STR_PAD_LEFT);
        })->toArray();

        // ✅ SOLO actualiza si no hay texto en alerta_contra
        foreach ($cedulas as $cedula) {
            DB::table('usuarios')
                ->where('cedula', $cedula)
                ->whereNull('alerta_contra')
                ->update(['alerta_contra' => '⚠️']);
        }

        $usuarios = DB::table('usuarios')
            ->whereIn('cedula', $cedulas)
            ->get();

        return redirect()->route('contra.view')->with('usuarios', $usuarios);
    }

    public function guardarNovedad(Request $request)
    {
        $request->validate([
            'cedulas' => 'required',
            'novedad' => 'nullable|string|max:1000',
        ]);

        $cedulas = explode(',', $request->cedulas);
        $novedad = $request->novedad;

        DB::table('usuarios')
            ->whereIn('cedula', $cedulas)
            ->update(['alerta_contra' => $novedad]);

        return redirect()->route('contra.view')->with('success', '¡Novedad guardada con éxito!');
    }
}
