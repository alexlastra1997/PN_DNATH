<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\CargoImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Cargo;
use App\Models\Usuario; // asegúrate de importar tu modelo
use App\Models\ReporteOrganico;


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

    public function cards()
    {
        // Agrupar por todo lo que está antes del guion
        $siglas = Cargo::selectRaw("SUBSTRING_INDEX(numero, '-', 1) as sigla")
        ->groupBy('sigla')
        ->orderBy('sigla')
        ->get()
        ->pluck('sigla');

        return view('cargos.cards', compact('siglas'));
    }





    public function detallePorSigla(Request $request, $sigla)
    {
        $query = Cargo::where('numero', 'LIKE', "$sigla-%");

        if ($request->filled('buscar')) {
            $query->where('cargo', 'like', '%' . $request->buscar . '%');
        }

        $cargos = $query->get()->map(function ($cargo) {
            // Buscar la función más similar en reporte_organico por texto
            $match = ReporteOrganico::all()->first(function ($rep) use ($cargo) {
                similar_text(
                    strtoupper(trim($cargo->cargo)),
                    strtoupper(trim($rep->funcion)),
                    $percent
                );
                return $percent >= 85; // puedes ajustar el porcentaje
            });

            $cargo->org_cantidad_ideal = $match->cantidad_ideal ?? null;
            $cargo->org_cantidad_real = $match->cantidad_real ?? null;
            $cargo->org_diferencia = $match->diferencia ?? null;

            return $cargo;
        });

        return view('cargos.detalle', [
            'numero' => $sigla,
            'cargos' => $cargos
        ]);
    }



    public function ocupadoPorUsuarios($cargoTexto)
    {
        $usuarios = Usuario::whereRaw("UPPER(funcion) LIKE ?", ['%' . strtoupper($cargoTexto) . '%'])
            ->whereRaw("UPPER(funcion) NOT LIKE '%SUB%'")
            ->get();

        return view('cargos.ocupado', [
            'cargo' => $cargoTexto,
            'usuarios' => $usuarios,
        ]);
    }




}

