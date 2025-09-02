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
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\ResumenOrganicoExport;


class ReporteOrganicoVisualController extends Controller
{

    public function index(Request $request)
    {
        // Base con filtros WHERE (sin HAVING)
        $base = DB::table('reporte_organico as ro')
            ->when($request->filled('servicio'), fn($q) => $q->where('ro.servicio_organico', 'like', '%' . $request->servicio . '%'))
            ->when($request->filled('nomenclatura'), fn($q) => $q->where('ro.nomenclatura_organico', 'like', '%' . $request->nomenclatura . '%'))
            ->when($request->filled('cargo'), fn($q) => $q->where('ro.cargo_organico', 'like', '%' . $request->cargo . '%'));

        // Filtro por estado con whereRaw usando el subquery del efectivo
        if ($request->filled('estado')) {
            $efectivoExpr = DB::raw('(SELECT COUNT(*)
                                  FROM usuarios u
                                  WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                                    AND u.funcion_efectiva = ro.cargo_organico)');
            if ($request->estado === 'VACANTE')   $base->whereRaw("{$efectivoExpr} < ro.numero_organico_ideal");
            if ($request->estado === 'COMPLETO')  $base->whereRaw("{$efectivoExpr} = ro.numero_organico_ideal");
            if ($request->estado === 'EXCEDIDO')  $base->whereRaw("{$efectivoExpr} > ro.numero_organico_ideal");
        }

        // === Totales globales (siempre calculados) ===
        $totales = (clone $base)
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((SELECT COUNT(*) FROM usuarios u
                                    WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                                      AND u.funcion_efectiva = ro.cargo_organico)),0) as total_efectivo')
            ->first();

        // === Nivel Adscrito (NADS) ===
        // Coincidimos prefijo NADS (insensible a mayúsculas), ej: "NADS - SCPN-...".
                $nivelAdscrito = (clone $base)
                    ->whereRaw("UPPER(TRIM(ro.nomenclatura_organico)) LIKE 'NADS%'")
                    ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
                    ->selectRaw('COALESCE(SUM((
                SELECT COUNT(*) FROM usuarios u
                WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                  AND u.funcion_efectiva     = ro.cargo_organico
            )),0) as total_efectivo')
                    ->first();

        // === Nivel de Apoyo (NAP, excluyendo NAPO) ===
        $nivelApoyo = (clone $base)
            ->whereRaw("UPPER(ro.nomenclatura_organico) LIKE 'NAP%'")
            ->whereRaw("UPPER(ro.nomenclatura_organico) NOT LIKE 'NAPO%'")
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
                SELECT COUNT(*) FROM usuarios u
                WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                  AND u.funcion_efectiva     = ro.cargo_organico
            )),0) as total_efectivo')
                    ->first();

        // === Nivel Asesor (NAS, excluyendo DINASED) ===
        $nivelAsesor = (clone $base)
            ->whereRaw("UPPER(ro.nomenclatura_organico) LIKE 'NAS%'")
            ->whereRaw("UPPER(ro.nomenclatura_organico) NOT LIKE 'DINASED%'")
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
                SELECT COUNT(*) FROM usuarios u
                WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                  AND u.funcion_efectiva     = ro.cargo_organico
            )),0) as total_efectivo')
                    ->first();

        // === Nivel de Coordinación (NCOORD) ===
        $nivelCoordinacion = (clone $base)
            ->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NCOORD%'")
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
                SELECT COUNT(*) FROM usuarios u
                WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                  AND u.funcion_efectiva     = ro.cargo_organico
            )),0) as total_efectivo')
                    ->first();

        // === Nivel Desconcentrado (NDESC) ===
        $nivelDesconcentrado = (clone $base)
            ->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NDESC%'")
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
                SELECT COUNT(*) FROM usuarios u
                WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                  AND u.funcion_efectiva     = ro.cargo_organico
            )),0) as total_efectivo')
                    ->first();

        // === Nivel Directivo (NDIREC) ===
        $nivelDirectivo = (clone $base)
            ->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NDIREC%'")
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
                SELECT COUNT(*) FROM usuarios u
                WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                  AND u.funcion_efectiva     = ro.cargo_organico
            )),0) as total_efectivo')
                    ->first();

        // === Nivel Operativo (NOPERA) ===
        $nivelOperativo = (clone $base)
            ->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NOPERA%'")
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
            SELECT COUNT(*) FROM usuarios u
            WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
              AND u.funcion_efectiva     = ro.cargo_organico
            )),0) as total_efectivo')
                    ->first();

        // === NDESC: ZONAL (servicio contiene PREV-ZONAL) ===
        $ndescZonal = (clone $base)
            ->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NDESC%'")
            ->whereRaw("UPPER(ro.servicio_organico) LIKE '%PREV-ZONAL%'")
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
        SELECT COUNT(*) FROM usuarios u
        WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
          AND u.funcion_efectiva     = ro.cargo_organico
    )),0) as total_efectivo')
            ->first();

// === NDESC: SUBZONAL (servicio contiene PREV-SZ) ===
        $ndescSubzonal = (clone $base)
            ->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NDESC%'")
            ->whereRaw("UPPER(ro.servicio_organico) LIKE '%PREV-SZ%'")
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
        SELECT COUNT(*) FROM usuarios u
        WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
          AND u.funcion_efectiva     = ro.cargo_organico
    )),0) as total_efectivo')
            ->first();

// === NDESC: DISTRITO - CIRCUITO - SUBCIRCUITO (servicio contiene PREV-D-C-S) ===
        $ndescDCS = (clone $base)
            ->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NDESC%'")
            ->whereRaw("UPPER(ro.servicio_organico) LIKE '%PREV-D-C-S%'")
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
        SELECT COUNT(*) FROM usuarios u
        WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
          AND u.funcion_efectiva     = ro.cargo_organico
    )),0) as total_efectivo')
            ->first();


// === Jefatura Preventiva (JPREV) — por NOMENCLATURA ===
        $jefPrev = (clone $base)
            ->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%JPREV%'")
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
        SELECT COUNT(*) FROM usuarios u
        WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
          AND u.funcion_efectiva     = ro.cargo_organico
    )),0) as total_efectivo')
            ->first();

// === Jefatura de Investigación (JINV) — por NOMENCLATURA ===
        $jefInv = (clone $base)
            ->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%JINV%'")
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
        SELECT COUNT(*) FROM usuarios u
        WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
          AND u.funcion_efectiva     = ro.cargo_organico
    )),0) as total_efectivo')
            ->first();

// === Jefatura de Inteligencia (JINT) — por NOMENCLATURA ===
        $jefInt = (clone $base)
            ->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%JINT%'")
            ->selectRaw('COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado')
            ->selectRaw('COALESCE(SUM((
        SELECT COUNT(*) FROM usuarios u
        WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
          AND u.funcion_efectiva     = ro.cargo_organico
    )),0) as total_efectivo')
            ->first();

        $estadoEfectivo = (clone $base)
            ->join('usuarios as u', function ($j) {
                $j->on('u.nomenclatura_efectiva', '=', 'ro.nomenclatura_organico')
                    ->on('u.funcion_efectiva',       '=', 'ro.cargo_organico');
            })
            ->whereNotNull('u.estado_efectivo')
            // Contiene en cualquier posición (cubre "POR EXCEDENTE")
            ->where(function ($w) {
                $w->whereRaw("UPPER(u.estado_efectivo) LIKE '%TRASLADO%TEMPORAL%'")
                    ->orWhereRaw("UPPER(u.estado_efectivo) LIKE '%TRASLADO%EVENTUAL%'")
                    ->orWhereRaw("UPPER(u.estado_efectivo) LIKE '%UNIDAD%DE%ORIGEN%'");
                // si quieres sumar "TRASLADO OCASIONAL" a EVENTUAL:
                // ->orWhereRaw("UPPER(u.estado_efectivo) LIKE '%TRASLADO%OCASIONAL%'");
            })
            ->selectRaw("
        CASE
          WHEN UPPER(u.estado_efectivo) LIKE '%TRASLADO%TEMPORAL%' THEN 'TRASLADO TEMPORAL
          'WHEN UPPER(u.estado_efectivo) LIKE '%TRASLADO%TEMPORAL%EN%EXCEDENTE%' THEN 'TRASLADO TEMPORAL'
          WHEN UPPER(u.estado_efectivo) LIKE '%TRASLADO%EVENTUAL%'
               /* OR UPPER(u.estado_efectivo) LIKE '%TRASLADO%OCASIONAL%' */ THEN 'TRASLADO EVENTUAL'
          WHEN UPPER(u.estado_efectivo) LIKE '%UNIDAD%DE%ORIGEN%'  THEN 'UNIDAD DE ORIGEN'
        END AS categoria
    ")
            // evita duplicar gente si el join “explota” por múltiples filas RO
            ->selectRaw("COUNT(DISTINCT u.id) AS total")
            ->groupBy('categoria')
            ->pluck('total', 'categoria');




        // Conteo por servicio (para tu acordeón nativo si lo usas)
        $conteoServicios = (clone $base)
            ->select('ro.servicio_organico', DB::raw('COUNT(*) as total'))
            ->groupBy('ro.servicio_organico')
            ->orderByDesc('total')
            ->get();

        // Listado principal
        $datos = (clone $base)
            ->select(
                'ro.servicio_organico',
                'ro.nomenclatura_organico',
                'ro.cargo_organico',
                DB::raw('ro.numero_organico_ideal as organico_aprobado'),
                DB::raw('(SELECT COUNT(*) FROM usuarios u
                      WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                        AND u.funcion_efectiva = ro.cargo_organico) as organico_efectivo')
            )
            ->paginate(50)
            ->withQueryString();

        return view('reporte_organico.visualizador', compact('datos','totales','nivelAdscrito','nivelDirectivo',
            'nivelOperativo', 'nivelDesconcentrado','nivelCoordinacion', 'nivelAsesor', 'nivelApoyo','ndescZonal', 'ndescSubzonal', 'ndescDCS','jefPrev',
            'jefInv','estadoEfectivo',
            'jefInt','conteoServicios'));
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


    public function exportResumenXlsx(Request $request)
    {
        $filename = 'resumen_organico_'.now()->format('Ymd_His').'.xlsx';
        $filters = $request->only(['servicio','nomenclatura','cargo','estado']);
        return Excel::download(new ResumenOrganicoExport($filters), $filename);
    }

}
