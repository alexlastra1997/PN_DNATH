<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BuscarVacanteController extends Controller
{
    public function index()
    {
        return $this->filtrar(new Request());
    }

    public function filtrar(Request $request)
    {
        // ---------- Filtros comunes ----------
        $filtroBase = Usuario::query()
            ->when($request->grado, fn($q) => $q->where('grado', $request->grado))
            ->when($request->subsistema, fn($q) => $q->where('subsistema_efectivo', $request->subsistema))
            ->when($request->tipo_personal, fn($q) => $q->where('tipo_personal', $request->tipo_personal))
            ->when($request->buscar, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('titulos', 'like', "%{$request->buscar}%")
                        ->orWhere('titulos_senescyt', 'like', "%{$request->buscar}%")
                        ->orWhere('capacitacion', 'like', "%{$request->buscar}%");
                });
            });

        if ($request->tiempo) {
            $filtroBase->whereNotNull('fecha_territorio_efectivo')
                ->whereRaw(
                    "TIMESTAMPDIFF(YEAR, fecha_territorio_efectivo, CURDATE()) " .
                    ($request->tiempo == 5 ? '> 4' : "= {$request->tiempo}")
                );
        }

        // ---------- Usuarios Ocupando ----------
        $usuarios = (clone $filtroBase)->paginate(0);

        // ---------- Emparejar con reporte_organico ----------
        $organicos = DB::table('reporte_organico')->get();
        $usuarios->getCollection()->transform(function ($u) use ($organicos) {
            foreach ($organicos as $org) {
                if (
                    stripos($u->nomenclatura_territorio_efectivo, $org->nomenclatura) !== false ||
                    stripos($org->nomenclatura, $u->nomenclatura_territorio_efectivo) !== false
                ) {
                    $u->unidad_organico = $org->unidad;
                    $u->funcion_organico = $org->funcion;
                    return $u;
                }
            }
            $u->unidad_organico = null;
            $u->funcion_organico = null;
            return $u;
        });

        // ---------- Vacantes Libres ----------
        $ocupadas = Usuario::pluck('nomenclatura_territorio_efectivo')->filter()->unique();

        $vacantes = DB::table('reporte_organico')
            ->when($request->grado, fn($q) => $q->where('grado', $request->grado))
            ->whereNotIn('nomenclatura', $ocupadas)
            ->paginate(10);

        // ---------- Duplicados ----------
        $funcionesClave = ['JEFE', 'COORDINADOR', 'SUBDIRECTOR', 'DIRECTOR'];

        $duplicadosRaw = (clone $filtroBase)
            ->whereIn('funcion', $funcionesClave)
            ->whereNotNull('nomenclatura_territorio_efectivo')
            ->get()
            ->groupBy('nomenclatura_territorio_efectivo')
            ->filter(fn($group) => $group->count() > 1)
            ->flatten();

        // Paginación manual para duplicados
        $perPage = 10;
        $currentPage = request('page_duplicados', 1);
        $duplicados = new \Illuminate\Pagination\LengthAwarePaginator(
            $duplicadosRaw->forPage($currentPage, $perPage),
            $duplicadosRaw->count(),
            $perPage,
            $currentPage,
            ['pageName' => 'page_duplicados']
        );

        // ---------- Selectores ----------
        $grados = Usuario::select('grado')->distinct()->orderBy('grado')->pluck('grado');
        $subsistemas = Usuario::select('subsistema_efectivo')->distinct()->orderBy('subsistema_efectivo')->pluck('subsistema_efectivo');
        $tipos = Usuario::select('tipo_personal')->distinct()->orderBy('tipo_personal')->pluck('tipo_personal');

        return view('buscar_vacante', [
            'usuarios'    => $usuarios,
            'vacantes'    => $vacantes,
            'duplicados'  => $duplicados,
            'grados'      => $grados,
            'subsistemas' => $subsistemas,
            'tipos'       => $tipos,
        ]);
    }
}
