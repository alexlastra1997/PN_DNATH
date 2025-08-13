<?php

namespace App\Http\Controllers;

use App\Models\ReporteOrganico;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteOrganicoExport;

class ReporteOrganicoVisualController extends Controller
{

public function index(Request $request)
{
    $query = DB::table('reporte_organico as ro')
        ->select(
            'ro.servicio_organico',
            'ro.nomenclatura_organico',
            'ro.cargo_organico',
            'ro.numero_organico_ideal as organico_aprobado',
            DB::raw('(SELECT COUNT(*) FROM usuarios u WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico AND u.funcion_efectiva = ro.cargo_organico) as organico_efectivo')
        );

    // Filtro: Servicio
    if ($request->filled('servicio')) {
        $query->where('ro.servicio_organico', 'like', '%' . $request->servicio . '%');
    }

    // Filtro: Nomenclatura
    if ($request->filled('nomenclatura')) {
        $query->where('ro.nomenclatura_organico', 'like', '%' . $request->nomenclatura . '%');
    }

    // Filtro: Cargo
    if ($request->filled('cargo')) {
        $query->where('ro.cargo_organico', 'like', '%' . $request->cargo . '%');
    }

    // Filtro por estado
    if ($request->filled('estado')) {
        $estado = $request->estado;
        switch ($estado) {
            case 'VACANTE':
                $query->havingRaw('organico_efectivo < organico_aprobado');
                break;
            case 'COMPLETO':
                $query->havingRaw('organico_efectivo = organico_aprobado');
                break;
            case 'EXCEDIDO':
                $query->havingRaw('organico_efectivo > organico_aprobado');
                break;
        }
    }

    // Paginación de 100
    
    $datos = $query->paginate(50)->withQueryString();

    return view('reporte_organico.visualizador', compact('datos'));
}


    public function buscarNomenclatura(Request $request)
    {
        $valor = $request->input('query');

        $datos = DB::table('reporte_organico')
            ->select(
                'servicio_organico',
                'nomenclatura_organico',
                'cargo_organico',
                DB::raw('SUM(numero_organico_ideal) as organico_aprobado')
            )
            ->where('nomenclatura_organico', 'LIKE', "%$valor%")
            ->groupBy('servicio_organico', 'nomenclatura_organico', 'cargo_organico')
            ->get();

        return response()->json([
            'html' => View::make('reporte_organico.partials.tabla', ['datos' => $datos])->render()
        ]);
    }

   public function ocupantes(Request $request)
    {
        $nomenclatura = $request->input('nomenclatura');
        $cargo = $request->input('cargo');

        $ocupantes = DB::table('usuarios')
            ->where('nomenclatura_efectiva', $nomenclatura)
            ->where('funcion_efectiva', $cargo)
            ->get();

        return view('reporte_organico.ocupantes', compact('ocupantes', 'nomenclatura', 'cargo'));
    }


   public function exportarExcel(Request $request)
{
    return Excel::download(new ReporteOrganicoExport($request), 'reporte_organico_filtrado.xlsx');
}

    public function obtenerEstadisticas(Request $request)
    {
        $query = DB::table('reporte_organico as ro')
            ->select(
                DB::raw('(SELECT COUNT(*) FROM usuarios u WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico AND u.funcion_efectiva = ro.cargo_organico) as organico_efectivo'),
                'ro.numero_organico_ideal as organico_aprobado'
            );

        // Filtros
        if ($request->filled('servicio')) {
            $query->where('ro.servicio_organico', 'like', '%' . $request->servicio . '%');
        }

        if ($request->filled('nomenclatura')) {
            $query->where('ro.nomenclatura_organico', 'like', '%' . $request->nomenclatura . '%');
        }

        if ($request->filled('cargo')) {
            $query->where('ro.cargo_organico', 'like', '%' . $request->cargo . '%');
        }

        $datos = $query->get();

        $vacantes = 0;
        $completos = 0;
        $excedidos = 0;

        foreach ($datos as $item) {
            if ($item->organico_efectivo < $item->organico_aprobado) {
                $vacantes++;
            } elseif ($item->organico_efectivo == $item->organico_aprobado) {
                $completos++;
            } else {
                $excedidos++;
            }
        }

        return response()->json([
            'vacantes' => $vacantes,
            'completos' => $completos,
            'excedidos' => $excedidos
        ]);
    }

}
