<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteOrganicoExport;
use App\Exports\ResumenOrganicoExport;

class ReporteOrganicoVisualController extends Controller
{
    // Pon a false si quieres máxima velocidad (omite banderitas/alertas costosas).
    private bool $computeAlerts = true;

    public function index(Request $request)
    {
        DB::connection()->disableQueryLog();

        // ====== Subqueries base ======

        // RO normalizado
        $roQuery = DB::table('reporte_organico as ro')
            ->selectRaw('ro.servicio_organico')
            ->selectRaw('ro.nomenclatura_organico')
            ->selectRaw('ro.cargo_organico')
            ->selectRaw('ro.grado_organico')
            ->selectRaw('ro.numero_organico_ideal')
            ->selectRaw('ro.subsistema')
            ->selectRaw('UPPER(TRIM(ro.nomenclatura_organico)) as ro_nom_norm')
            ->selectRaw("CONCAT(SUBSTRING_INDEX(UPPER(TRIM(ro.nomenclatura_organico)),'-',5),'-') as ro_base_nom")
            ->selectRaw('UPPER(TRIM(ro.cargo_organico)) as ro_cargo_norm');

        // Usuarios: REGLA 1 (match exacto cargo + nomenclatura)
        $uExactQuery = DB::table('usuarios as u')
            ->selectRaw("UPPER(TRIM(u.funcion_efectiva)) as cargo_norm")
            ->selectRaw("UPPER(TRIM(u.nomenclatura_efectiva)) as nom_norm")
            ->selectRaw('COUNT(*) as cnt')
            ->groupBy('cargo_norm','nom_norm');

        // Usuarios “left” (para construir heredados)
        $uLeftQuery = DB::table('usuarios as u')
            ->selectRaw("UPPER(TRIM(u.funcion_efectiva)) as cargo_norm")
            ->selectRaw("UPPER(TRIM(u.nomenclatura_efectiva)) as nom_norm")
            ->selectRaw("CONCAT(SUBSTRING_INDEX(UPPER(TRIM(u.nomenclatura_efectiva)),'-',5),'-') as base_nom");

        // RO distintos por (cargo_norm, nom_norm) para saber si existe RO exacta
        $roDistinctQuery = DB::table('reporte_organico as ro')
            ->selectRaw("UPPER(TRIM(ro.cargo_organico)) as cargo_norm, UPPER(TRIM(ro.nomenclatura_organico)) as nom_norm")
            ->distinct();

        // Usuarios: REGLA 2 (heredado por base) SOLO si NO tienen RO exacta
        $uInheritedQuery = DB::query()->fromSub($uLeftQuery, 'ul')
            ->leftJoinSub($roDistinctQuery, 'rod', function ($j) {
                $j->on('rod.cargo_norm','=','ul.cargo_norm')
                    ->on('rod.nom_norm'  ,'=','ul.nom_norm');
            })
            ->whereNull('rod.nom_norm')
            ->selectRaw('ul.cargo_norm, ul.base_nom, COUNT(*) as cnt')
            ->groupBy('ul.cargo_norm','ul.base_nom'); //

        // ====== BASE (JOIN + FILTROS) ======
        $listBase = DB::query()
            ->fromSub($roQuery, 'rb')
            ->leftJoinSub($uExactQuery, 'ue', function ($j) {
                $j->on('ue.cargo_norm','=','rb.ro_cargo_norm')
                    ->on('ue.nom_norm'  ,'=','rb.ro_nom_norm');
            })
            ->leftJoinSub($uInheritedQuery, 'ui', function ($j) {
                $j->on('ui.cargo_norm','=','rb.ro_cargo_norm')
                    ->on('ui.base_nom'  ,'=','rb.ro_base_nom');
            }); //

        // ====== Filtros múltiples (normalizados) ======
        $normArr = fn(array $arr) => array_values(array_filter(array_map(
            fn($v)=>mb_strtoupper(trim((string)$v)), (array)$arr
        ), fn($v)=>$v!==''));

        // Servicio
        $servicios = $normArr($request->input('servicio', []));
        if ($servicios) {
            $listBase->whereIn(DB::raw('TRIM(UPPER(rb.servicio_organico))'), $servicios);
        }

        // Nomenclatura
        $nomenclaturas = $normArr($request->input('nomenclatura', []));
        if ($nomenclaturas) {
            $listBase->whereIn(DB::raw('TRIM(UPPER(rb.nomenclatura_organico))'), $nomenclaturas);
        }

        // Cargo
        $cargos = $normArr($request->input('cargo', []));
        if ($cargos) {
            $listBase->whereIn(DB::raw('TRIM(UPPER(rb.cargo_organico))'), $cargos);
        }

        // Subsistema
        $subsistemas = $normArr($request->input('subsistema', []));
        if ($subsistemas) {
            $listBase->whereIn(DB::raw('TRIM(UPPER(rb.subsistema))'), $subsistemas);
        }

        // Grado Orgánico (CSV). Conservamos tu lógica REGEXP
        $grados = $normArr($request->input('grado_organico', []));
        if ($grados) {
            $listBase->where(function($w) use ($grados) {
                foreach ($grados as $g) {
                    $pattern = '(^|,)[[:space:]]*'.preg_quote($g,'/').'([[:space:]]*,|$)';
                    $w->orWhereRaw("UPPER(rb.grado_organico) REGEXP ?", [$pattern]);
                }
            });
        } //

        // ====== Estados (VACANTE/COMPLETO/EXCEDIDO) ======
        $efectivoExpr = "COALESCE(ue.cnt,0) + CASE WHEN rb.ro_nom_norm = rb.ro_base_nom THEN COALESCE(ui.cnt,0) ELSE 0 END";
        $estados = array_values(array_filter((array)$request->input('estado', []),
            fn($v)=>in_array($v, ['VACANTE','COMPLETO','EXCEDIDO'], true)
        ));
        if ($estados) {
            $listBase->where(function($w) use ($estados, $efectivoExpr) {
                foreach ($estados as $estado) {
                    if ($estado === 'VACANTE')  $w->orWhereRaw("$efectivoExpr <  rb.numero_organico_ideal");
                    if ($estado === 'COMPLETO') $w->orWhereRaw("$efectivoExpr =  rb.numero_organico_ideal");
                    if ($estado === 'EXCEDIDO') $w->orWhereRaw("$efectivoExpr >  rb.numero_organico_ideal");
                }
            });
        } //

        // ====== SELECT (listado principal) ======
        $select = $listBase->cloneWithoutBindings(['select', 'orders'])
            ->selectRaw('rb.servicio_organico, rb.nomenclatura_organico, rb.cargo_organico, rb.subsistema')
            ->selectRaw('rb.numero_organico_ideal as organico_aprobado')
            ->selectRaw("$efectivoExpr as organico_efectivo");

        if ($this->computeAlerts) {
            // Marcador simple de alerta (p. ej. si grados no cuadran)
            $select->selectRaw("CASE WHEN TRIM(rb.grado_organico) = '' THEN NULL ELSE 1 END as tiene_alerta");
        }

        // Orden y paginación
        $datos = $select
            ->orderBy('rb.servicio_organico')
            ->orderBy('rb.nomenclatura_organico')
            ->orderBy('rb.cargo_organico')
            ->paginate(100)
            ->appends($request->query());

        // ====== Totales (para cabecera) ======
        $totales = DB::query()
            ->fromSub($roQuery, 'rb')
            ->leftJoinSub($uExactQuery, 'ue', function ($j) {
                $j->on('ue.cargo_norm','=','rb.ro_cargo_norm')
                    ->on('ue.nom_norm'  ,'=','rb.ro_nom_norm');
            })
            ->leftJoinSub($uInheritedQuery, 'ui', function ($j) {
                $j->on('ui.cargo_norm','=','rb.ro_cargo_norm')
                    ->on('ui.base_nom'  ,'=','rb.ro_base_nom');
            })
            ->selectRaw('SUM(rb.numero_organico_ideal) as total_aprobado')
            ->selectRaw("SUM(COALESCE(ue.cnt,0) + CASE WHEN rb.ro_nom_norm = rb.ro_base_nom THEN COALESCE(ui.cnt,0) ELSE 0 END) as total_efectivo")
            ->first();

        // ====== Resumen por subsistema (para modal) ======
        $statsSubsistema = DB::query()
            ->fromSub($roQuery, 'rb')
            ->leftJoinSub($uExactQuery, 'ue', function ($j) {
                $j->on('ue.cargo_norm','=','rb.ro_cargo_norm')
                    ->on('ue.nom_norm'  ,'=','rb.ro_nom_norm');
            })
            ->leftJoinSub($uInheritedQuery, 'ui', function ($j) {
                $j->on('ui.cargo_norm','=','rb.ro_cargo_norm')
                    ->on('ui.base_nom'  ,'=','rb.ro_base_nom');
            })
            ->selectRaw('rb.subsistema')
            ->selectRaw('SUM(rb.numero_organico_ideal) as total_aprobado')
            ->selectRaw("SUM($efectivoExpr) as total_efectivo")
            ->selectRaw("SUM(CASE WHEN $efectivoExpr < rb.numero_organico_ideal THEN 1 ELSE 0 END) as cargos_vacantes")
            ->selectRaw("SUM(CASE WHEN $efectivoExpr = rb.numero_organico_ideal THEN 1 ELSE 0 END) as cargos_completos")
            ->selectRaw("SUM(CASE WHEN $efectivoExpr > rb.numero_organico_ideal THEN 1 ELSE 0 END) as cargos_excedidos")
            ->groupBy('rb.subsistema')
            ->get();

        // ====== Opciones de filtros (para los multiselect del blade) ======
        // Saco de la tabla RO para no inflar el query principal
        $opcionesServicio     = DB::table('reporte_organico')->whereNotNull('servicio_organico')->distinct()->orderBy('servicio_organico')->pluck('servicio_organico')->toArray();
        $opcionesNomenclatura = DB::table('reporte_organico')->whereNotNull('nomenclatura_organico')->distinct()->orderBy('nomenclatura_organico')->pluck('nomenclatura_organico')->toArray();
        $opcionesCargo        = DB::table('reporte_organico')->whereNotNull('cargo_organico')->distinct()->orderBy('cargo_organico')->pluck('cargo_organico')->toArray();
        $opcionesSubsistema   = DB::table('reporte_organico')->whereNotNull('subsistema')->distinct()->orderBy('subsistema')->pluck('subsistema')->toArray();

        return view('reporte_organico.visualizador', [
            'datos'                => $datos,
            'totales'              => $totales,
            'statsSubsistema'      => $statsSubsistema,
            'opcionesServicio'     => $opcionesServicio,
            'opcionesNomenclatura' => $opcionesNomenclatura,
            'opcionesCargo'        => $opcionesCargo,
            'opcionesSubsistema'   => $opcionesSubsistema,
        ]);
    }

    public function ocupantes(Request $request)
    {
        // Normalización
        $nomenclatura = (string) $request->query('nomenclatura', '');
        $cargo        = (string) $request->query('cargo', '');
        $nomNorm      = mb_strtoupper(trim($nomenclatura));
        $cargoNorm    = mb_strtoupper(trim($cargo));

        // Ocupantes exactos
        $ocupantesExact = DB::table('usuarios as u')
            ->select('u.cedula','u.grado','u.apellidos_nombres','u.estado_efectivo')
            ->whereRaw('UPPER(TRIM(u.funcion_efectiva)) = ?', [$cargoNorm])
            ->whereRaw('UPPER(TRIM(u.nomenclatura_efectiva)) = ?', [$nomNorm])
            ->orderBy('u.grado')->orderBy('u.apellidos_nombres')->get();

        // Info del cargo exacto (para mostrar grados permitidos y nro ideal)
        $infoCargo = DB::table('reporte_organico as ro')
            ->select(
                'ro.servicio_organico',
                'ro.nomenclatura_organico',
                'ro.cargo_organico',
                'ro.grado_organico',
                'ro.personal_organico',
                'ro.numero_organico_ideal',
                'ro.subsistema'
            )
            ->whereRaw('UPPER(TRIM(ro.cargo_organico)) = ?', [$cargoNorm])
            ->whereRaw('UPPER(TRIM(ro.nomenclatura_organico)) = ?', [$nomNorm])
            ->orderBy('ro.servicio_organico')
            ->get(); //

        if ($ocupantesExact->count() > 0) {
            // Grados permitidos (del RO exacto)
            $gradosPermitidos = [];
            foreach ($infoCargo as $fila) {
                $tokens = preg_split('/[,;]+/u', (string)($fila->grado_organico ?? ''), -1, PREG_SPLIT_NO_EMPTY);
                foreach ($tokens as $t) {
                    $t = mb_strtoupper(trim($t));
                    if ($t !== '') $gradosPermitidos[$t] = true;
                }
            }
            $gradosPermitidos = array_keys($gradosPermitidos);

            // No usar compact con alias
            return view('reporte_organico.ocupantes', [
                'ocupantes'        => $ocupantesExact,
                'nomenclatura'     => $nomenclatura,
                'cargo'            => $cargo,
                'infoCargo'        => $infoCargo,
                'gradosPermitidos' => $gradosPermitidos,
            ]); //
        }

        // ------- Regla 2: HERENCIA/BASE (función igual + misma base de distrito) -------
        $parts = array_values(array_filter(explode('-', $nomNorm), fn($p) => $p !== ''));
        $base  = implode('-', array_slice($parts, 0, 5)) . '-';

        $ocupantesBase = DB::table('usuarios as u')
            ->select('u.cedula','u.grado','u.apellidos_nombres','u.estado_efectivo')
            ->whereRaw('UPPER(TRIM(u.funcion_efectiva)) = ?', [$cargoNorm])
            ->whereRaw("CONCAT(SUBSTRING_INDEX(UPPER(TRIM(u.nomenclatura_efectiva)),'-',5),'-') = ?", [$base])
            ->orderBy('u.grado')->orderBy('u.apellidos_nombres')->get(); //

        if ($infoCargo->isEmpty()) {
            // Si no hay RO exacto, intentamos una cabecera de la misma base+función
            $infoCargo = DB::table('reporte_organico as ro')
                ->select(
                    'ro.servicio_organico',
                    'ro.nomenclatura_organico',
                    'ro.cargo_organico',
                    'ro.grado_organico',
                    'ro.personal_organico',
                    'ro.numero_organico_ideal'
                )
                ->whereRaw('UPPER(TRIM(ro.cargo_organico)) = ?', [$cargoNorm])
                ->whereRaw("CONCAT(SUBSTRING_INDEX(UPPER(TRIM(ro.nomenclatura_organico)),'-',5),'-') = ?", [$base])
                ->limit(1)
                ->get();
        }

        // Igual construimos grados permitidos desde infoCargo
        $gradosPermitidos = [];
        foreach ($infoCargo as $fila) {
            $tokens = preg_split('/[,;]+/u', (string)($fila->grado_organico ?? ''), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($tokens as $t) {
                $t = mb_strtoupper(trim($t));
                if ($t !== '') $gradosPermitidos[$t] = true;
            }
        }
        $gradosPermitidos = array_keys($gradosPermitidos);

        return view('reporte_organico.ocupantes', [
            'ocupantes'        => $ocupantesBase,
            'nomenclatura'     => $nomenclatura,
            'cargo'            => $cargo,
            'infoCargo'        => $infoCargo,
            'gradosPermitidos' => $gradosPermitidos,
        ]);
    }

    public function exportarExcel(Request $request)
    {
        // ====== Subqueries base (idénticas al index) ======
        $roQuery = DB::table('reporte_organico as ro')
            ->selectRaw('ro.servicio_organico')
            ->selectRaw('ro.nomenclatura_organico')
            ->selectRaw('ro.cargo_organico')
            ->selectRaw('ro.grado_organico')
            ->selectRaw('ro.numero_organico_ideal')
            ->selectRaw('ro.subsistema')
            ->selectRaw('UPPER(TRIM(ro.nomenclatura_organico)) as ro_nom_norm')
            ->selectRaw("CONCAT(SUBSTRING_INDEX(UPPER(TRIM(ro.nomenclatura_organico)),'-',5),'-') as ro_base_nom")
            ->selectRaw('UPPER(TRIM(ro.cargo_organico)) as ro_cargo_norm'); //

        $uExactQuery = DB::table('usuarios as u')
            ->selectRaw("UPPER(TRIM(u.funcion_efectiva)) as cargo_norm")
            ->selectRaw("UPPER(TRIM(u.nomenclatura_efectiva)) as nom_norm")
            ->selectRaw('COUNT(*) as cnt')
            ->groupBy('cargo_norm','nom_norm'); //

        $uLeftQuery = DB::table('usuarios as u')
            ->selectRaw("UPPER(TRIM(u.funcion_efectiva)) as cargo_norm")
            ->selectRaw("UPPER(TRIM(u.nomenclatura_efectiva)) as nom_norm")
            ->selectRaw("CONCAT(SUBSTRING_INDEX(UPPER(TRIM(u.nomenclatura_efectiva)),'-',5),'-') as base_nom"); //

        $roDistinctQuery = DB::table('reporte_organico as ro')
            ->selectRaw("UPPER(TRIM(ro.cargo_organico)) as cargo_norm, UPPER(TRIM(ro.nomenclatura_organico)) as nom_norm")
            ->distinct(); //

        $uInheritedQuery = DB::query()->fromSub($uLeftQuery, 'ul')
            ->leftJoinSub($roDistinctQuery, 'rod', function ($j) {
                $j->on('rod.cargo_norm','=','ul.cargo_norm')
                    ->on('rod.nom_norm'  ,'=','ul.nom_norm');
            })
            ->whereNull('rod.nom_norm')
            ->selectRaw('ul.cargo_norm, ul.base_nom, COUNT(*) as cnt')
            ->groupBy('ul.cargo_norm','ul.base_nom'); //

        $q = DB::query()
            ->fromSub($roQuery, 'rb')
            ->leftJoinSub($uExactQuery, 'ue', function ($j) {
                $j->on('ue.cargo_norm','=','rb.ro_cargo_norm')
                    ->on('ue.nom_norm'  ,'=','rb.ro_nom_norm');
            })
            ->leftJoinSub($uInheritedQuery, 'ui', function ($j) {
                $j->on('ui.cargo_norm','=','rb.ro_cargo_norm')
                    ->on('ui.base_nom'  ,'=','rb.ro_base_nom');
            }); //

        // ====== Aplicar los mismos filtros que en index (normalizados) ======
        $normArr = fn(array $arr) => array_values(array_filter(array_map(
            fn($v)=>mb_strtoupper(trim((string)$v)), (array)$arr
        ), fn($v)=>$v!==''));

        $servicios     = $normArr($request->input('servicio', []));
        $nomenclaturas = $normArr($request->input('nomenclatura', []));
        $cargos        = $normArr($request->input('cargo', []));
        $subsistemas   = $normArr($request->input('subsistema', []));
        $grados        = $normArr($request->input('grado_organico', []));
        $efectivoExpr  = "COALESCE(ue.cnt,0) + CASE WHEN rb.ro_nom_norm = rb.ro_base_nom THEN COALESCE(ui.cnt,0) ELSE 0 END";

        if ($servicios)     $q->whereIn(DB::raw('TRIM(UPPER(rb.servicio_organico))'), $servicios);
        if ($nomenclaturas) $q->whereIn(DB::raw('TRIM(UPPER(rb.nomenclatura_organico))'), $nomenclaturas);
        if ($cargos)        $q->whereIn(DB::raw('TRIM(UPPER(rb.cargo_organico))'), $cargos);
        if ($subsistemas)   $q->whereIn(DB::raw('TRIM(UPPER(rb.subsistema))'), $subsistemas);

        if ($grados) {
            $q->where(function($w) use ($grados) {
                foreach ($grados as $g) {
                    $pattern = '(^|,)[[:space:]]*'.preg_quote($g,'/').'([[:space:]]*,|$)';
                    $w->orWhereRaw("UPPER(rb.grado_organico) REGEXP ?", [$pattern]);
                }
            }); //
        }

        $estados = array_values(array_filter((array)$request->input('estado', []),
            fn($v)=>in_array($v, ['VACANTE','COMPLETO','EXCEDIDO'], true)
        ));
        if ($estados) {
            $q->where(function($w) use ($estados,$efectivoExpr){
                foreach ($estados as $estado) {
                    if ($estado==='VACANTE')  $w->orWhereRaw("$efectivoExpr <  rb.numero_organico_ideal");
                    if ($estado==='COMPLETO') $w->orWhereRaw("$efectivoExpr =  rb.numero_organico_ideal");
                    if ($estado==='EXCEDIDO') $w->orWhereRaw("$efectivoExpr >  rb.numero_organico_ideal");
                }
            }); //
        }

        // ====== Selección de columnas para export ======
        $rows = $q->cloneWithoutBindings(['select','orders'])
            ->selectRaw('rb.servicio_organico as Servicio')
            ->selectRaw('rb.nomenclatura_organico as Nomenclatura')
            ->selectRaw('rb.cargo_organico as Cargo')
            ->selectRaw('rb.subsistema as Subsistema')
            ->selectRaw('rb.numero_organico_ideal as Aprobado')
            ->selectRaw("$efectivoExpr as Efectivo")
            ->orderBy('rb.servicio_organico')
            ->orderBy('rb.nomenclatura_organico')
            ->orderBy('rb.cargo_organico')
            ->get();

        // Usa tu export existente (ajusta el constructor si tu clase espera otra cosa)
        $filename = 'reporte_organico_'.now()->format('Ymd_His').'.xlsx';
        return Excel::download(new \App\Exports\ReporteOrganicoExport($rows), $filename);
    }

}
