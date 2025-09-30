<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class OrganicoStatusController extends Controller
{
    /** ====================== Fragmentos SQL reutilizables ====================== */
    private function sqlFragments(): array
    {
        // Normalizaciones base
        $nomRo = "UPPER(TRIM(ro.nomenclatura_organico))";
        $nomU  = "UPPER(TRIM(u.nomenclatura_efectiva))";

        // Quitar puntuación común
        $cargoNormRo = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(TRIM(ro.cargo_organico)),'.',' '),'-',' '),'(',' '),')',' '),'/',' ')";
        $funcNormU   = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(UPPER(TRIM(u.funcion_efectiva)),'.',' '),'-',' '),'(',' '),')',' '),'/',' ')";

        // Prefijo del servicio (primera palabra antes de "-")
        $servRaizRo = "SUBSTRING_INDEX(UPPER(TRIM(ro.servicio_organico)),'-',1)";

        // === CASE canónico (incluye VICERRECTOR/RECTOR) ===
        // Nota: el ORDEN importa (VICERRECTOR/RECTOR antes de DIRECTOR)
        $canonCargoRo = <<<SQL
CASE
  WHEN {$cargoNormRo} REGEXP '(SUB[[:space:]]*COMANDAN(TE|TA)|SUB[[:space:]]*CM?D?TE|SUBCMTE|SUBCMDTE)' THEN 'SUBCOMANDANTE'
  WHEN {$cargoNormRo} REGEXP '(COMANDAN(TE|TA)|\\bCMTE\\b|\\bCMDTE\\b|\\bCMDT\\b|COMANDO)' THEN 'COMANDANTE'

  WHEN {$cargoNormRo} REGEXP '(SUB[[:space:]]*DIRECTOR(A)?|\\bSUBDIR\\b|VICE[[:space:]]*DIRECTOR(A)?|VICEDIRECTOR(A)?)' THEN 'SUBDIRECTOR'
  WHEN {$cargoNormRo} REGEXP '(^|[^A-Z0-9ÁÉÍÓÚÑ])(V(IC(E|ER))?[[:space:]]*RECTOR(A)?)([^A-Z0-9ÁÉÍÓÚÑ]|$)' THEN 'VICERRECTOR'
  WHEN {$cargoNormRo} REGEXP '(^|[^A-Z0-9ÁÉÍÓÚÑ])(RECTOR(A)?)([^A-Z0-9ÁÉÍÓÚÑ]|$)' THEN 'RECTOR'
  WHEN {$cargoNormRo} REGEXP '(^|[^A-Z0-9ÁÉÍÓÚÑ])(DIRECTOR(A)?|\\bDIR\\b)([^A-Z0-9ÁÉÍÓÚÑ]|$)' THEN 'DIRECTOR'

  WHEN {$cargoNormRo} REGEXP '(SUB[[:space:]]*JEF(E|A)|\\bSJEFE\\b|SUBJEFE)' THEN 'SUBJEFE'
  WHEN {$cargoNormRo} REGEXP '(^|[^A-Z0-9ÁÉÍÓÚÑ])(JEF(E|A)|\\bJF\\b|JEFATURA)([^A-Z0-9ÁÉÍÓÚÑ]|$)' THEN 'JEFE'
  ELSE NULL
END
SQL;

        $canonFuncU = <<<SQL
CASE
  WHEN {$funcNormU} REGEXP '(SUB[[:space:]]*COMANDAN(TE|TA)|SUB[[:space:]]*CM?D?TE|SUBCMTE|SUBCMDTE)' THEN 'SUBCOMANDANTE'
  WHEN {$funcNormU} REGEXP '(COMANDAN(TE|TA)|\\bCMTE\\b|\\bCMDTE\\b|\\bCMDT\\b|COMANDO)' THEN 'COMANDANTE'

  WHEN {$funcNormU} REGEXP '(SUB[[:space:]]*DIRECTOR(A)?|\\bSUBDIR\\b|VICE[[:space:]]*DIRECTOR(A)?|VICEDIRECTOR(A)?)' THEN 'SUBDIRECTOR'
  WHEN {$funcNormU} REGEXP '(^|[^A-Z0-9ÁÉÍÓÚÑ])(V(IC(E|ER))?[[:space:]]*RECTOR(A)?)([^A-Z0-9ÁÉÍÓÚÑ]|$)' THEN 'VICERRECTOR'
  WHEN {$funcNormU} REGEXP '(^|[^A-Z0-9ÁÉÍÓÚÑ])(RECTOR(A)?)([^A-Z0-9ÁÉÍÓÚÑ]|$)' THEN 'RECTOR'
  WHEN {$funcNormU} REGEXP '(^|[^A-Z0-9ÁÉÍÓÚÑ])(DIRECTOR(A)?|\\bDIR\\b)([^A-Z0-9ÁÉÍÓÚÑ]|$)' THEN 'DIRECTOR'

  WHEN {$funcNormU} REGEXP '(SUB[[:space:]]*JEF(E|A)|\\bSJEFE\\b|SUBJEFE)' THEN 'SUBJEFE'
  WHEN {$funcNormU} REGEXP '(^|[^A-Z0-9ÁÉÍÓÚÑ])(JEF(E|A)|\\bJF\\b|JEFATURA)([^A-Z0-9ÁÉÍÓÚÑ]|$)' THEN 'JEFE'
  ELSE NULL
END
SQL;

        // === "Específico": quitamos títulos para comparar el resto ===
        $baseRo = $cargoNormRo;
        $baseU  = $funcNormU;

        $especRo = "
TRIM(
  REPLACE(
    REPLACE(
      REPLACE(
        REPLACE(
          REPLACE(
            REPLACE(
              REPLACE(
                REPLACE(
                  REPLACE(
                    REPLACE(
                      REPLACE(
                        REPLACE(
                          REPLACE(
                            REPLACE(
                              {$baseRo},
                              'VICER RECTOR',''
                            ), 'VICERRECTOR',''
                          ), 'VICERECTOR',''
                        ), 'VICE RECTOR',''
                      ), 'VICE- RECTOR',''
                    ), 'VICE-RECTOR',''
                  ), 'RECTORA',''
                ), 'RECTOR',''
              ), 'SUB COMANDANTE',''
            ), 'COMANDANTE',''
          ), 'SUB DIRECTORA',''
        ), 'SUB DIRECTOR',''
      ), 'DIRECTORA',''
    ), 'DIRECTOR',''
  )
)
";

        $especU = "
TRIM(
  REPLACE(
    REPLACE(
      REPLACE(
        REPLACE(
          REPLACE(
            REPLACE(
              REPLACE(
                REPLACE(
                  REPLACE(
                    REPLACE(
                      REPLACE(
                        REPLACE(
                          REPLACE(
                            REPLACE(
                              {$baseU},
                              'VICER RECTOR',''
                            ), 'VICERRECTOR',''
                          ), 'VICERECTOR',''
                        ), 'VICE RECTOR',''
                      ), 'VICE- RECTOR',''
                    ), 'VICE-RECTOR',''
                  ), 'RECTORA',''
                ), 'RECTOR',''
              ), 'SUB COMANDANTE',''
            ), 'COMANDANTE',''
          ), 'SUB DIRECTORA',''
        ), 'SUB DIRECTOR',''
      ), 'DIRECTORA',''
    ), 'DIRECTOR',''
  )
)
";

        return compact(
            'nomRo','nomU',
            'cargoNormRo','funcNormU',
            'canonCargoRo','canonFuncU',
            'servRaizRo',
            'especRo','especU'
        );
    }

    /** ====================== /mapa-direc (cards por prefijo) ====================== */
    public function index(Request $request)
    {
        $cargosCanon = ['RECTOR','VICERRECTOR','DIRECTOR','SUBDIRECTOR','COMANDANTE','SUBCOMANDANTE','JEFE','SUBJEFE'];

        $datos = Cache::remember('mapa-direc:index:all', 60, function () use ($cargosCanon) {
            extract($this->sqlFragments());

            $org = DB::table('reporte_organico as ro')->selectRaw("
                ro.id,
                ro.servicio_organico,
                ro.nomenclatura_organico,
                ro.cargo_organico,
                ro.grado_organico,
                COALESCE(ro.numero_organico_ideal,0) AS numero_organico_ideal,
                {$nomRo} AS nom_ro,
                {$servRaizRo} AS servicio_raiz,
                {$canonCargoRo} AS cargo_canon,
                {$especRo} AS especifico_ro
            ");

            $usu = DB::table('usuarios as u')->selectRaw("
                u.id,
                {$nomU} AS nom_u,
                {$canonFuncU} AS funcion_canon,
                {$especU} AS especifico_u
            ");

            return DB::query()
                ->fromSub($org, 'ro2')
                ->leftJoinSub($usu, 'u2', function ($join) {
                    $join->on(function ($j) {
                        $j->on('u2.nom_u', 'like', DB::raw("CONCAT(ro2.nom_ro,'%')"))
                            ->orOn('ro2.nom_ro', 'like', DB::raw("CONCAT(u2.nom_u,'%')"));
                    });
                    $join->whereColumn('u2.funcion_canon', 'ro2.cargo_canon');
                    $join->where(function ($k) {
                        $k->whereRaw("(NULLIF(ro2.especifico_ro,'') IS NULL AND NULLIF(u2.especifico_u,'') IS NULL)")
                            ->orWhere('u2.especifico_u', 'like', DB::raw("CONCAT('%', ro2.especifico_ro, '%')"))
                            ->orWhere('ro2.especifico_ro', 'like', DB::raw("CONCAT('%', u2.especifico_u, '%')"));
                    });
                })
                ->whereIn('ro2.cargo_canon', $cargosCanon)
                ->selectRaw("
                    ro2.id,
                    ro2.servicio_organico,
                    ro2.servicio_raiz,
                    ro2.nomenclatura_organico,
                    ro2.cargo_organico,
                    ro2.grado_organico,
                    ro2.numero_organico_ideal AS organico_aprobado,
                    COUNT(DISTINCT u2.id) AS organico_efectivo
                ")
                ->groupBy('ro2.id','ro2.servicio_organico','ro2.servicio_raiz','ro2.nomenclatura_organico','ro2.cargo_organico','ro2.grado_organico','ro2.numero_organico_ideal')
                ->orderBy('ro2.servicio_raiz')
                ->orderBy('ro2.servicio_organico')
                ->orderBy('ro2.nomenclatura_organico')
                ->get();
        });

        return view('mapa_direc.index', compact('datos'));
    }

    /** =================== /mapa-direc/raiz/{raiz} (tablas + popup) =================== */
    public function raiz(string $raiz)
    {
        $raiz = strtoupper(trim(urldecode($raiz)));
        $cargosCanon = ['RECTOR','VICERRECTOR','DIRECTOR','SUBDIRECTOR','COMANDANTE','SUBCOMANDANTE','JEFE','SUBJEFE'];
        extract($this->sqlFragments());

        $orgServicio = DB::table('reporte_organico as ro')->selectRaw("
                ro.id,
                ro.servicio_organico,
                ro.nomenclatura_organico,
                ro.cargo_organico,
                ro.grado_organico,
                COALESCE(ro.numero_organico_ideal,0) AS numero_organico_ideal,
                {$nomRo} AS nom_ro,
                {$servRaizRo} AS servicio_raiz,
                {$canonCargoRo} AS cargo_canon,
                {$especRo} AS especifico_ro
            ")
            ->whereRaw("SUBSTRING_INDEX(UPPER(TRIM(ro.servicio_organico)),'-',1) = ?", [$raiz]);

        $usuFull = DB::table('usuarios as u')->selectRaw("
                u.id AS u_id,
                u.cedula,
                u.apellidos_nombres,
                u.grado,
                u.promocion,
                u.nomenclatura_efectiva,
                u.funcion_efectiva,
                u.estado_efectivo,
                u.fecha_efectiva,
                {$nomU} AS nom_u,
                {$canonFuncU} AS funcion_canon,
                {$especU} AS especifico_u
            ");

        // Tabla 1: orgánico
        $organicos = DB::query()
            ->fromSub($orgServicio, 'ro2')
            ->leftJoinSub($usuFull, 'u2', function ($join) {
                $join->on(function ($j) {
                    $j->on('u2.nom_u', 'like', DB::raw("CONCAT(ro2.nom_ro,'%')"))
                        ->orOn('ro2.nom_ro', 'like', DB::raw("CONCAT(u2.nom_u,'%')"));
                })
                    ->whereColumn('u2.funcion_canon', 'ro2.cargo_canon')
                    ->where(function ($k) {
                        $k->whereRaw("(NULLIF(ro2.especifico_ro,'') IS NULL AND NULLIF(u2.especifico_u,'') IS NULL)")
                            ->orWhere('u2.especifico_u', 'like', DB::raw("CONCAT('%', ro2.especifico_ro, '%')"))
                            ->orWhere('ro2.especifico_ro', 'like', DB::raw("CONCAT('%', u2.especifico_u, '%')"));
                    });
            })
            ->whereIn('ro2.cargo_canon', $cargosCanon)
            ->selectRaw("
                ro2.id,
                ro2.servicio_organico,
                ro2.nomenclatura_organico,
                ro2.cargo_organico,
                ro2.grado_organico,
                ro2.numero_organico_ideal AS organico_aprobado,
                COUNT(DISTINCT u2.u_id) AS organico_efectivo
            ")
            ->groupBy('ro2.id','ro2.servicio_organico','ro2.nomenclatura_organico','ro2.cargo_organico','ro2.grado_organico','ro2.numero_organico_ideal')
            ->orderBy('ro2.servicio_organico')
            ->orderBy('ro2.nomenclatura_organico')
            ->get();

        // Tabla 2: usuarios (popup + alerta)
        $usuarios = DB::query()
            ->fromSub($orgServicio, 'ro2')
            ->joinSub($usuFull, 'u2', function ($join) {
                $join->on(function ($j) {
                    $j->on('u2.nom_u', 'like', DB::raw("CONCAT(ro2.nom_ro,'%')"))
                        ->orOn('ro2.nom_ro', 'like', DB::raw("CONCAT(u2.nom_u,'%')"));
                })
                    ->whereColumn('u2.funcion_canon', 'ro2.cargo_canon')
                    ->where(function ($k) {
                        $k->whereRaw("(NULLIF(ro2.especifico_ro,'') IS NULL AND NULLIF(u2.especifico_u,'') IS NULL)")
                            ->orWhere('u2.especifico_u', 'like', DB::raw("CONCAT('%', ro2.especifico_ro, '%')"))
                            ->orWhere('ro2.especifico_ro', 'like', DB::raw("CONCAT('%', u2.especifico_u, '%')"));
                    });
            })
            ->whereIn('ro2.cargo_canon', $cargosCanon)
            ->selectRaw("
                u2.u_id,
                MAX(u2.cedula)                 AS cedula,
                MAX(u2.apellidos_nombres)      AS apellidos_nombres,
                MAX(u2.grado)                  AS grado,
                MAX(u2.promocion)              AS promocion,
                MAX(u2.nomenclatura_efectiva)  AS nomenclatura_efectiva,
                MAX(u2.funcion_efectiva)       AS funcion_efectiva,
                MAX(u2.estado_efectivo)        AS estado_efectivo,
                MAX(u2.fecha_efectiva)         AS fecha_efectiva,
                COUNT(DISTINCT ro2.id)         AS vacantes_vinculadas,

                GROUP_CONCAT(DISTINCT ro2.grado_organico ORDER BY ro2.grado_organico SEPARATOR ', ') AS grados_validos,

                CASE
                    WHEN SUM(CASE WHEN ro2.grado_organico = u2.grado THEN 1 ELSE 0 END) = 0
                    THEN 1 ELSE 0
                END AS alerta_grado
            ")
            ->groupBy('u2.u_id')
            ->orderBy('apellidos_nombres')
            ->get();

        return view('mapa_direc.servicio', [
            'servicio'  => $raiz,
            'organicos' => $organicos,
            'usuarios'  => $usuarios,
        ]);
    }

    /** (opcional) Detalle de una fila orgánica específica */
    public function show($id)
    {
        extract($this->sqlFragments());

        $ro = DB::table('reporte_organico as ro')->selectRaw("
                ro.*,
                {$nomRo} AS nom_ro,
                {$canonCargoRo} AS cargo_canon,
                {$especRo} AS especifico_ro
            ")
            ->where('ro.id', $id)
            ->first();

        if (!$ro) abort(404, 'No se encontró el registro orgánico.');

        $usu = DB::table('usuarios as u')->selectRaw("
                u.id AS u_id,
                u.cedula,
                u.apellidos_nombres,
                u.grado,
                u.promocion,
                u.nomenclatura_efectiva,
                u.funcion_efectiva,
                u.estado_efectivo,
                u.fecha_efectiva,
                {$nomU} AS nom_u,
                {$canonFuncU} AS funcion_canon,
                {$especU} AS especifico_u
            ");

        $ocupantes = DB::query()
            ->fromSub($usu, 'u2')
            ->where('u2.funcion_canon', $ro->cargo_canon)
            ->where(function ($q) use ($ro) {
                $q->where('u2.nom_u', 'like', $ro->nom_ro.'%')
                    ->orWhere('u2.nom_u', 'like', '%'.$ro->nom_ro.'%')
                    ->orWhereRaw('? LIKE CONCAT(u2.nom_u, \"%\")', [$ro->nom_ro]);
            })
            ->where(function ($k) use ($ro) {
                $k->whereRaw("(NULLIF(?, '') IS NULL AND NULLIF(u2.especifico_u,'') IS NULL)", [$ro->especifico_ro])
                    ->orWhere('u2.especifico_u', 'like', '%'.($ro->especifico_ro ?? '').'%')
                    ->orWhereRaw("? LIKE CONCAT('%', u2.especifico_u, '%')", [$ro->especifico_ro ?? '']);
            })
            ->selectRaw("
                u2.u_id,
                MAX(u2.cedula)                AS cedula,
                MAX(u2.apellidos_nombres)     AS apellidos_nombres,
                MAX(u2.grado)                 AS grado,
                MAX(u2.promocion)             AS promocion,
                MAX(u2.nomenclatura_efectiva) AS nomenclatura_efectiva,
                MAX(u2.funcion_efectiva)      AS funcion_efectiva,
                MAX(u2.estado_efectivo)       AS estado_efectivo,
                MAX(u2.fecha_efectiva)        AS fecha_efectiva
            ")
            ->groupBy('u2.u_id')
            ->orderBy('apellidos_nombres')
            ->get();

        return view('mapa_direc.show', ['ro' => $ro, 'ocupantes' => $ocupantes]);
    }
}
