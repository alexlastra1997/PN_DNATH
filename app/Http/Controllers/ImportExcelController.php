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
    public function showContra(Request $request)
    {
        // 🔎 Buscador backend (SQL) para rendimiento
        $q = trim((string) $request->get('q', ''));

        $alertasQuery = DB::table('usuarios')
            ->select('cedula', 'apellidos_nombres', 'grado', 'alerta_contra')
            ->whereNotNull('alerta_contra')
            ->where('alerta_contra', '!=', '')
            ->where('alerta_contra', '!=', '⚠️');

        if ($q !== '') {
            $alertasQuery->where(function ($w) use ($q) {
                $w->where('cedula', 'like', "%{$q}%")
                    ->orWhere('apellidos_nombres', 'like', "%{$q}%")
                    ->orWhere('grado', 'like', "%{$q}%")
                    ->orWhere('alerta_contra', 'like', "%{$q}%");
            });
        }

        $alertas = $alertasQuery
            ->orderBy('grado')
            ->orderBy('apellidos_nombres')
            ->paginate(20)
            ->appends(['q' => $q]);

        $totalAlertas = DB::table('usuarios')
            ->whereNotNull('alerta_contra')
            ->where('alerta_contra', '!=', '')
            ->where('alerta_contra', '!=', '⚠️')
            ->count();

        return view('contra', compact('alertas', 'q', 'totalAlertas'));
    }

    public function importContra(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $collection = Excel::toCollection(null, $request->file('file'))->first();

        $cedulas = $collection->pluck(0)
            ->filter()
            ->map(function ($cedula) {
                $cedula = trim((string) $cedula);
                return str_pad($cedula, 10, '0', STR_PAD_LEFT);
            })
            ->unique()
            ->values()
            ->toArray();

        // Marca ⚠️ solo si está vacío
        DB::table('usuarios')
            ->whereIn('cedula', $cedulas)
            ->where(function ($q) {
                $q->whereNull('alerta_contra')
                    ->orWhere('alerta_contra', '');
            })
            ->update(['alerta_contra' => '⚠️']);

        return redirect()
            ->route('contra.view')
            ->with('success', '✅ Cédulas importadas y marcadas correctamente (solo donde estaba vacío).');
    }

    public function guardarNovedad(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string',
            'novedad' => 'nullable|string|max:1000',
        ]);

        $cedula = str_pad(trim($request->cedula), 10, '0', STR_PAD_LEFT);
        $novedad = $request->novedad;

        $nuevoValor = (is_null($novedad) || trim($novedad) === '') ? null : $novedad;

        DB::table('usuarios')
            ->where('cedula', $cedula)
            ->update(['alerta_contra' => $nuevoValor]);

        return redirect()->route('contra.view')->with('success', '✅ Alerta actualizada correctamente.');
    }
}
