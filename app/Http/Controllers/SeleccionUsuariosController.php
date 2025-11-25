<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SeleccionUsuariosController extends Controller
{
    // ==== Vista de carga (con fallback a usuarios.opciones) ====
    public function opciones()
    {
        $view = view()->exists('seleccion.opciones') ? 'seleccion.opciones' : 'usuarios.opciones';
        return view($view);
    }

    // ==== Procesa Excel y guarda universo de cédulas en sesión ====
    public function masivo(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:xlsx,xls,csv'
        ]);

        $archivo = $request->file('archivo');
        $spreadsheet = IOFactory::load($archivo->getRealPath());
        $hoja = $spreadsheet->getActiveSheet();
        $filas = $hoja->toArray(null, true, true, true);

        // Normalizador de cédulas: solo dígitos y pad a 10
        $norm = function (?string $v): ?string {
            if ($v === null) return null;
            $d = preg_replace('/\D+/', '', (string)$v);
            if ($d === '') return null;
            return str_pad(substr($d, -10), 10, '0', STR_PAD_LEFT);
        };

        $cedulas = [];
        $esPrimera = true;
        foreach ($filas as $fila) {
            // Si la primera fila es encabezado, sáltala (detecta no-dígitos)
            if ($esPrimera) {
                $esPrimera = false;
                $posible = $fila['A'] ?? '';
                $soloDig = preg_replace('/\D+/', '', (string)$posible);
                if ($soloDig === '' || strlen($soloDig) < 8) {
                    // parece encabezado, no lo tomo
                    continue;
                }
            }
            $valor = $norm($fila['A'] ?? null);
            if ($valor !== null) {
                $cedulas[] = $valor;
            }
        }

        $cedulas = collect($cedulas)->filter()->unique()->values()->all();

        if (empty($cedulas)) {
            return back()->with('error', 'No se encontraron cédulas válidas en el archivo.');
        }

        // Guarda en sesión
        $request->session()->put('cedulas_filtradas', $cedulas);

        // Redirige a resultados (prefijo seleccion.*)
        return redirect()->route('seleccion.resultados');
    }

    // ==== Listado con filtros (siempre restringido por universo) ====
    public function resultado(Request $request)
    {
        // Botón "Limpiar" => borra universo
        if ($request->boolean('clear')) {
            $request->session()->forget('cedulas_filtradas');
            return redirect()->route('seleccion.resultados');
        }

        // --- Parámetros UI ---
        $alertasSeleccionadas = (array) $request->input('alertas', []);
        $estado               = $request->input('estado', 'todos');
        $q                    = trim((string) $request->input('q', ''));
        $seleccionFiltro      = $request->input('seleccion', 'todos'); // todos | seleccionados | no_seleccionados

        // NUEVO: selects múltiples
        $proyLicSeleccion     = array_values(array_filter((array) $request->input('proyeccion_licencia', [])));
        $fechaEfSeleccion     = array_values(array_filter((array) $request->input('fecha_efectiva', [])));

        // --- Lista blanca de columnas de alerta ---
        $opcionesAlertas = [
            'contrato_estudios',
            'enf_catast_sp',
            'enf_catast_conyuge_hijos',
            'discapacidad_sp',
            'discapacidad_conyuge_hijos',
            'alertas',
            'alertas_problemas_salud',
            'novedad_situacion',
            'alerta_devengacion',
            'alerta_marco_legal',
            'observacion_tenencia',
            'pase_ucp_ccp_cpl',
            'FaseMaternidadUDGA',
            'fase_maternidad',
            'maternidad',
        ];
        $alertasSeleccionadas = array_values(array_intersect($alertasSeleccionadas, $opcionesAlertas));

        // --- Normalizador de cédulas (mismo de masivo) ---
        $norm = function ($v) {
            $d = preg_replace('/\D+/', '', (string)$v);
            if ($d === '') return null;
            return str_pad(substr($d, -10), 10, '0', STR_PAD_LEFT);
        };

        // --- Universo de cédulas: primero query (hidden), si no, sesión ---
        $cedulas = [];
        $param = $request->input('cedulas', null);
        if (is_string($param) && $param !== '') {
            foreach (explode(',', $param) as $c) {
                $n = $norm($c);
                if ($n !== null) $cedulas[] = $n;
            }
        } elseif (is_array($param)) {
            foreach ($param as $c) {
                $n = $norm($c);
                if ($n !== null) $cedulas[] = $n;
            }
        }
        if (empty($cedulas)) {
            $fromSession = (array) $request->session()->get('cedulas_filtradas', []);
            foreach ($fromSession as $c) {
                $n = $norm($c);
                if ($n !== null) $cedulas[] = $n;
            }
        }

        // --- Base de consulta ---
        $base = DB::table('usuarios');

        // ** GARANTÍA: si NO hay universo, NO mostrar nada **
        if (empty($cedulas)) {
            $base->whereRaw('1=0'); // nunca devuelve filas
        } else {
            $base->whereIn('cedula', $cedulas);
        }

        // Búsqueda libre
        if ($q !== '') {
            $p = "%{$q}%";
            $base->where(function ($qq) use ($p) {
                $qq->where('cedula', 'like', $p)
                    ->orWhere('apellidos_nombres', 'like', $p)
                    ->orWhere('grado', 'like', $p)
                    ->orWhere('provincia_trabaja', 'like', $p);
            });
        }

        // Helper de "valor significativo" en columnas de alerta
        $colTieneValor = function($q, $col) {
            $q->whereNotNull($col)
                ->whereRaw("TRIM(CAST(`$col` AS CHAR)) <> ''")
                ->whereRaw("UPPER(TRIM(CAST(`$col` AS CHAR))) NOT IN ('NO','N/A','NA')")
                ->whereRaw("TRIM(CAST(`$col` AS CHAR)) <> '0'");
        };

        // Filtro por alertas
        if (!empty($alertasSeleccionadas)) {
            $base->where(function ($q2) use ($alertasSeleccionadas, $colTieneValor) {
                foreach ($alertasSeleccionadas as $col) {
                    $q2->orWhere(function ($c) use ($col, $colTieneValor) {
                        $colTieneValor($c, $col);
                    });
                }
            });
        } elseif ($estado === 'alerta') {
            $base->where(function ($q2) use ($opcionesAlertas, $colTieneValor) {
                foreach ($opcionesAlertas as $col) {
                    $q2->orWhere(function ($c) use ($col, $colTieneValor) {
                        $colTieneValor($c, $col);
                    });
                }
            });
        }

        // Estado activo
        if ($estado === 'activo') {
            $base->where(function ($q3) {
                $q3->whereNull('novedad_situacion')
                    ->orWhere('novedad_situacion', '')
                    ->orWhereRaw("UPPER(TRIM(`novedad_situacion`)) = 'ACTIVO'");
            });
        }

        // ===== NUEVO: Filtros por selects múltiples =====

        // Proyección de licencia
        if (!empty($proyLicSeleccion)) {
            $base->whereIn('proyeccion_licencia', $proyLicSeleccion);
        }

        // Fecha efectiva (si el campo es DATETIME/DATE usamos whereDate)
        if (!empty($fechaEfSeleccion)) {
            $base->where(function($w) use ($fechaEfSeleccion) {
                foreach ($fechaEfSeleccion as $d) {
                    $w->orWhereDate('fecha_efectiva', $d);
                }
            });
        }

        // Selección (carrito ya existentes)
        $carritoAptos    = (array) $request->session()->get('carrito.apto', []);
        $carritoNoAptos  = (array) $request->session()->get('carrito.no_apto', []);
        $carritoAptosCed   = array_keys($carritoAptos);
        $carritoNoAptosCed = array_keys($carritoNoAptos);

        if ($seleccionFiltro === 'seleccionados') {
            $cedSel = array_values(array_unique(array_merge($carritoAptosCed, $carritoNoAptosCed)));
            $base->whereIn('cedula', $cedSel ?: ['__none__']);
        } elseif ($seleccionFiltro === 'no_seleccionados') {
            $cedSel = array_values(array_unique(array_merge($carritoAptosCed, $carritoNoAptosCed)));
            if (!empty($cedSel)) {
                $base->whereNotIn('cedula', $cedSel);
            }
        }

        // Columnas a mostrar (incluyo proyeccion_licencia y fecha_efectiva por si luego las quieres en tabla)
        $selectCols = [
            'cedula', 'apellidos_nombres', 'grado',
            'estado_civil', 'promocion', 'novedad_situacion',
            'contrato_estudios', 'enf_catast_sp', 'enf_catast_conyuge_hijos',
            'discapacidad_sp', 'discapacidad_conyuge_hijos', 'alertas',
            'alertas_problemas_salud','alerta_devengacion','alerta_marco_legal','observacion_tenencia',
            'pase_ucp_ccp_cpl','FaseMaternidadUDGA','fase_maternidad','maternidad',
            // nuevos (opcionales):
            'proyeccion_licencia','fecha_efectiva',
        ];

        // Paginación (preserva filtros + universo)
        $usuarios = $base->select($selectCols)
            ->paginate(50)
            ->appends(array_merge(
                $request->query(),
                !empty($cedulas) ? ['cedulas' => implode(',', $cedulas)] : []
            ));

        // ===== Opciones para los dropdowns (distintos valores) =====

        // Base para opciones: restringimos al universo, sin depender de lo que el usuario escoja en esos mismos selects
        $optBase = DB::table('usuarios');
        if (empty($cedulas)) {
            $optBase->whereRaw('1=0');
        } else {
            $optBase->whereIn('cedula', $cedulas);
        }

        // Proyección licencia: valores distintos no vacíos
        $proyeccionOpciones = DB::table('usuarios')
            ->when(!empty($cedulas), fn($q) => $q->whereIn('cedula', $cedulas), fn($q) => $q->whereRaw('1=0'))
            ->whereNotNull('proyeccion_licencia')
            ->whereRaw("TRIM(CAST(`proyeccion_licencia` AS CHAR)) <> ''")
            ->distinct()
            ->orderBy('proyeccion_licencia')
            ->pluck('proyeccion_licencia')
            ->values()
            ->all();

        // Fecha efectiva: valores distintos (si es DATETIME/DATE, tomamos sólo Y-m-d)
        $fechaEfOpciones = DB::table('usuarios')
            ->when(!empty($cedulas), fn($q) => $q->whereIn('cedula', $cedulas), fn($q) => $q->whereRaw('1=0'))
            ->whereNotNull('fecha_efectiva')
            ->distinct()
            ->orderBy('fecha_efectiva', 'desc')
            ->pluck('fecha_efectiva')
            ->map(function($v){
                try {
                    return \Carbon\Carbon::parse($v)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return (string)$v;
                }
            })
            ->unique()
            ->values()
            ->all();

        return view('seleccion.resultado', [
            'usuarios'               => $usuarios,
            'opcionesAlertas'        => $opcionesAlertas,
            'alertasSeleccionadas'   => $alertasSeleccionadas,
            'estadoSeleccionado'     => $estado,
            'q'                      => $q,
            'cedulas'                => $cedulas, // para el hidden

            // nuevos para selects
            'proyeccionOpciones'     => $proyeccionOpciones,
            'proyLicSeleccion'       => $proyLicSeleccion,
            'fechaEfOpciones'        => $fechaEfOpciones,
            'fechaEfSeleccion'       => $fechaEfSeleccion,
            'seleccionFiltro'        => $seleccionFiltro,

            // para pintar estado de carrito en tabla y contadores
            'carritoAptosCed'        => $carritoAptosCed,
            'carritoNoAptosCed'      => $carritoNoAptosCed,
        ]);
    }




    // === Normalizador de cédula (helper) ===
    private function normCedula(?string $v): ?string {
        if ($v === null) return null;
        $d = preg_replace('/\D+/', '', (string)$v);
        if ($d === '') return null;
        return str_pad(substr($d, -10), 10, '0', STR_PAD_LEFT);
    }

    /**
     * POST /seleccion/calificar
     * Guarda en sesión al usuario como APTO o NO_APTO.
     */
    public function calificar(Request $request)
    {
        $request->validate([
            'cedula'          => 'required',
            'estado'          => 'required|in:APTO,NO_APTO',
            'novedad'         => 'nullable|in:SIN_NOVEDAD,NOVEDAD',
            'detalle_novedad' => 'nullable|string|required_if:novedad,NOVEDAD',
        ], [
            'detalle_novedad.required_if' => 'Debes ingresar el detalle de la novedad.',
        ]);

        // Normaliza cédula
        $ced = $this->normCedula((string)$request->input('cedula'));
        if (!$ced) {
            return response()->json(['status' => 'error', 'message' => 'Cédula inválida'], 422);
        }

        // Busca datos mínimos del usuario
        $u = DB::table('usuarios')
            ->select('cedula','apellidos_nombres','grado')
            ->where('cedula', $ced)
            ->first();

        if (!$u) {
            return response()->json(['status' => 'error', 'message' => 'Usuario no encontrado'], 404);
        }

        $novedad  = $request->input('novedad', 'SIN_NOVEDAD');
        $detalle  = $request->input('detalle_novedad', null);

        // Carrito en sesión
        $apto   = $request->session()->get('carrito.apto', []);
        $noApto = $request->session()->get('carrito.no_apto', []);

        if (isset($apto[$ced])) {
            return response()->json(['status' => 'exists', 'message' => 'Este usuario ya fue calificado como APTO']);
        }
        if (isset($noApto[$ced])) {
            return response()->json(['status' => 'exists', 'message' => 'Este usuario ya fue calificado como NO APTO']);
        }

        $payload = [
            'cedula'            => $u->cedula,
            'apellidos_nombres' => $u->apellidos_nombres,
            'grado'             => $u->grado,
            'novedad'           => $novedad,                 // SIN_NOVEDAD | NOVEDAD
            'detalle_novedad'   => $novedad === 'NOVEDAD' ? ($detalle ?? '') : null,
        ];

        if ($request->input('estado') === 'APTO') {
            $apto[$ced] = $payload;
            $request->session()->put('carrito.apto', $apto);
            return response()->json(['status' => 'ok', 'message' => 'Agregado como APTO', 'estado' => 'APTO']);
        } else {
            $noApto[$ced] = $payload;
            $request->session()->put('carrito.no_apto', $noApto);
            return response()->json(['status' => 'ok', 'message' => 'Agregado como NO APTO', 'estado' => 'NO_APTO']);
        }
    }

    /**
     * GET /seleccion/carrito
     * Muestra las dos tablas: Aptos y No Aptos (desde sesión)
     */
    public function carrito(Request $request)
    {
        $aptos   = $request->session()->get('carrito.apto', []);
        $noAptos = $request->session()->get('carrito.no_apto', []);

        // Si quieres orden alfabético:
        ksort($aptos);
        ksort($noAptos);

        // Fallback: seleccion.carrito -> usuarios.carrito si no existe aún
        $view = view()->exists('seleccion.carrito') ? 'seleccion.carrito' : 'usuarios.carrito';

        return view($view, [
            'aptos'   => $aptos,
            'noAptos' => $noAptos,
        ]);
    }

    public function carritoEliminar(Request $request)
    {
        $request->validate([
            'cedula' => 'required',
            'estado' => 'required|in:APTO,NO_APTO',
        ]);

        // Normaliza cédula
        $ced = $this->normCedula($request->input('cedula'));
        if (!$ced) {
            return response()->json(['status' => 'error', 'message' => 'Cédula inválida'], 422);
        }

        $key   = $request->input('estado') === 'APTO' ? 'carrito.apto' : 'carrito.no_apto';
        $items = $request->session()->get($key, []);

        if (isset($items[$ced])) {
            unset($items[$ced]);
            $request->session()->put($key, $items);
            return response()->json(['status' => 'ok', 'message' => 'Eliminado del carrito']);
        }

        return response()->json(['status' => 'missing', 'message' => 'No estaba en el carrito']);
    }

    public function informePdf(Request $request)
    {
        // ===== 1) Datos del carrito =====
        $aptos   = array_values($request->session()->get('carrito.apto', []));
        $noAptos = array_values($request->session()->get('carrito.no_apto', []));

        // ===== 2) Datos del formulario =====
        $cap   = trim($request->input('capacitacion', ''));
        $mod   = strtoupper(trim($request->input('modalidad', '')));
        $fiIn  = $request->input('fecha_inicio');
        $ffIn  = $request->input('fecha_fin');
        $nroOverride = $request->input('nro_total_override');

        // Firmas
        $elab_nombre = trim($request->input('elaborado_nombre', ''));
        $elab_grado  = trim($request->input('elaborado_grado', ''));
        $elab_cargo  = trim($request->input('elaborado_cargo', ''));
        $rev_nombre  = trim($request->input('revisado_nombre', ''));
        $rev_grado   = trim($request->input('revisado_grado', ''));
        $rev_cargo   = trim($request->input('revisado_cargo', ''));

        // ===== 3) Fechas formato “01 DE SEPTIEMBRE DE 2025” =====
        $fmtDate = function ($d) {
            try {
                return mb_strtoupper(\Carbon\Carbon::parse($d)->locale('es')->translatedFormat('d \\de F \\de Y'), 'UTF-8');
            } catch (\Throwable $e) { return e($d); }
        };
        $fi = $fmtDate($fiIn);
        $ff = $fmtDate($ffIn);

        // ===== 4) Totales =====
        $totalA = count($aptos);
        $totalN = count($noAptos);
        $nroA   = is_numeric($nroOverride) ? (int)$nroOverride : $totalA;
        $nroN   = is_numeric($nroOverride) ? (int)$nroOverride : $totalN;

        $generado = now()->timezone(config('app.timezone', 'America/Guayaquil'))->format('d/m/Y H:i');

        // ===== 5) Header/Footer images a base64 =====
        $toBase64 = function (string $relPath): ?string {
            $path = public_path($relPath);
            if (!is_file($path)) return null;
            $mime = mime_content_type($path) ?: 'image/png';
            $data = base64_encode(file_get_contents($path));
            return "data:{$mime};base64,{$data}";
        };
        $headerImg = $toBase64('images/pn.png');
        $footerImg = $toBase64('images/pn2.png');

        // ===== 6) Enriquecer filas con UNIDAD y FUNCIÓN (1 sola consulta) =====
        $cedsA = array_map(fn($r) => (string)($r['cedula'] ?? ''), $aptos);
        $cedsN = array_map(fn($r) => (string)($r['cedula'] ?? ''), $noAptos);
        $allCedulas = array_values(array_unique(array_filter(array_merge($cedsA, $cedsN))));
        $extraByCed = [];
        if (!empty($allCedulas)) {
            $extraRows = DB::table('usuarios')
                ->select('cedula','nomenclatura_efectiva','funcion_efectiva')
                ->whereIn('cedula', $allCedulas)
                ->get();
            foreach ($extraRows as $er) {
                $extraByCed[$er->cedula] = [
                    'unidad'  => $er->nomenclatura_efectiva,
                    'funcion' => $er->funcion_efectiva,
                ];
            }
        }

        // ===== 7) Construir filas HTML (nuevas columnas) =====
        $rows = function (array $list) use ($extraByCed) {
            $out = ''; $i = 1;
            foreach ($list as $row) {
                $ced   = e($row['cedula'] ?? '');
                $grado = e($row['grado'] ?? '');
                $nom   = e(mb_strtoupper($row['apellidos_nombres'] ?? '', 'UTF-8'));
                $ex    = $extraByCed[$row['cedula'] ?? ''] ?? ['unidad'=>null,'funcion'=>null];
                $unidad  = e(mb_strtoupper($ex['unidad']  ?? '', 'UTF-8'));
                $funcion = e(mb_strtoupper($ex['funcion'] ?? '', 'UTF-8'));
                $obs   = ($row['novedad'] ?? 'SIN_NOVEDAD') === 'NOVEDAD'
                    ? 'NOVEDAD: ' . e($row['detalle_novedad'] ?? '')
                    : 'SIN NOVEDAD';

                $out .= "<tr>
                        <td>{$i}</td>
                        <td class='mono'>{$ced}</td>
                        <td>{$grado}</td>
                        <td>{$nom}</td>
                        <td>{$unidad}</td>
                        <td>{$funcion}</td>
                        <td>{$obs}</td>
                     </tr>";
                $i++;
            }
            if ($i === 1) {
                $out .= "<tr><td colspan='7' class='empty'>Sin registros</td></tr>";
            }
            return $out;
        };
        $rowsA = $rows($aptos);
        $rowsN = $rows($noAptos);

        // ===== 8) Firmas (dos columnas, centrado) =====
        $firmasHTML = function ($en, $eg, $ec, $rn, $rg, $rc) {
            $en = e($en); $eg = e($eg); $ec = e($ec);
            $rn = e($rn); $rg = e($rg); $rc = e($rc);
            return "
        <div class='gap-lg'></div>
        <div class='signatures'>
          <div class='sign-col'>
            <div class='sign-title'>ELABORADO POR:</div>
            <div class='sign-space'></div>
            <div class='sign-name'>{$en}</div>
            <div class='sign-grade'>{$eg}</div>
            <div class='sign-role'>{$ec}</div>
          </div>
          <div class='sign-col'>
            <div class='sign-title'>REVISADO POR:</div>
            <div class='sign-space'></div>
            <div class='sign-name'>{$rn}</div>
            <div class='sign-grade'>{$rg}</div>
            <div class='sign-role'>{$rc}</div>
          </div>
        </div>";
        };

        // ===== 9) Tags header/footer =====
        $headerTag = $headerImg ? '<img src="'.$headerImg.'" style="height:70px; width:100%; object-fit:contain;">' : '<div style="height:70px"></div>';
        $footerTag = $footerImg ? '<img src="'.$footerImg.'" style="height:60px; width:100%; object-fit:contain;">' : '<div style="height:60px"></div>';

        $capEsc = e($cap);

        // ===== 10) HTML + CSS =====
        $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Informe de Validación</title>
<style>
  @page { margin: 120px 28px 95px 28px; }
  body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }

  header { position: fixed; top: -95px; left: 0; right: 0; height: 95px; text-align:center; }
  footer { position: fixed; bottom: -85px; left: 0; right: 0; height: 85px; text-align:center; }
  .pagenum:before { content: counter(page); }

  h1 { font-size: 16px; margin: 0 0 6px; text-align:center; font-weight: 700; }
  h2 { font-size: 13px; margin: 16px 0 8px; font-weight: 700; }

 /* Bloque informativo sin líneas y ANEXO debajo, alineado a la derecha del bloque */
.info{
  margin: 6px 0 10px;
  padding: 4px 2px;
}
.info-row{ font-size: 11px; margin: 2px 0; }
.info-label{ display:inline-block; width: 120px; font-weight: 700; text-transform: uppercase; }

/* contenedor para colocar el anexo debajo del bloque */
.info-anexo{
  text-align: right;          /* ANEXO a la derecha, pero debajo del bloque */
  margin-top: 6px;
}

.anexo-box{
  display: inline-block;
  border: 2px solid #000;
  padding: 6px 12px;
  font-weight: 700;
  font-size: 12px;
  background: #fff;
}


  /* Tabla general */
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #ccc; padding: 6px 8px; vertical-align: top; }
  th { background: #f3f4f6; font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: .02em; }
  .mono { font-family: 'DejaVu Sans Mono', monospace; }
  .empty { text-align:center; color:#666; }

  /* Fila de título encima de las columnas (como en la imagen) */
  .head-title th {
    background: #fff; border: 1px solid #000; font-weight: 700; font-size: 11px; text-align:center;
  }

  /* Firmas (dos columnas, centrado, sin bordes en el espacio) */
  .signatures{ display: table; width: 100%; table-layout: fixed; margin: 16px 0 8px; }
  .sign-col{ display: table-cell; width: 50%; padding: 0 12px; text-align: center; vertical-align: top; }
  .sign-title{ font-weight: 700; text-transform: uppercase; font-size: 11px; margin-bottom: 4px; }
  .sign-space{ height: 80px; border: none; margin-bottom: 6px; background: transparent; }
  .sign-name{  font-weight: 700;  margin: 0; line-height: 1.1; }
  .sign-grade{ font-size: 11px; margin: 0; line-height: 1.1; }
  .sign-role{  font-size: 11px; font-weight: 700; margin: 0; line-height: 1.1; }

  .gap-lg{ height: 14px; }  /* espacio entre tabla y firmas */
</style>
</head>
<body>
  <header>{$headerTag}</header>
  <footer>
    {$footerTag}
    <div style="font-size:10px; color:#666; margin-top:4px;">Página <span class="pagenum"></span></div>
  </footer>

  <main>
    <h1>INFORME DE VALIDACIÓN</h1>
    <div style="text-align:center; font-size:10px; color:#666;">Generado: {$generado}</div>

    <!-- ===== ANEXO 1 (APTOS) ===== -->
    <div class="info">
      <div class="info-row"><span class="info-label">CAPACITACIÓN:</span> {$capEsc}</div>
      <div class="info-row"><span class="info-label">Nro. TOTAL:</span> {$nroA}</div>
      <div class="info-row"><span class="info-label">MODALIDAD:</span> {$mod}</div>
      <div class="info-row"><span class="info-label">FECHA INICIO:</span> {$fi}</div>
      <div class="info-row"><span class="info-label">FECHA FIN:</span> {$ff}</div>
      <div class="info-anexo"><span class="anexo-box">ANEXO 1</span></div>
    </div>

    <table>
      <thead>
        <tr class="head-title"><th colspan="7">NÓMINA DE SERVIDORES POLICIALES APTOS EN EL PROCESO DE VALIDACIÓN</th></tr>
        <tr>
          <th style="width:36px">#</th>
          <th style="width:110px">CÉDULA</th>
          <th style="width:80px">GRADO</th>
          <th>NOMBRES</th>
          <th>UNIDAD</th>
          <th>FUNCIÓN</th>
          <th style="width:120px">OBSERVACIÓN</th>
        </tr>
      </thead>
      <tbody>
        {$rowsA}
      </tbody>
    </table>

    {$firmasHTML($elab_nombre,$elab_grado,$elab_cargo,$rev_nombre,$rev_grado,$rev_cargo)}

    <div style="page-break-before: always;"></div>

    <!-- ===== ANEXO 2 (NO APTOS) ===== -->
    <div class="info">
      <div class="info-row"><span class="info-label">CAPACITACIÓN:</span> {$capEsc}</div>
      <div class="info-row"><span class="info-label">Nro. TOTAL:</span> {$nroN}</div>
      <div class="info-row"><span class="info-label">MODALIDAD:</span> {$mod}</div>
      <div class="info-row"><span class="info-label">FECHA INICIO:</span> {$fi}</div>
      <div class="info-row"><span class="info-label">FECHA FIN:</span> {$ff}</div>
      <div class="info-anexo"><span class="anexo-box">ANEXO 2</span></div>
    </div>

    <table>
      <thead>
        <tr class="head-title"><th colspan="7">NÓMINA DE SERVIDORES POLICIALES NO APTOS EN EL PROCESO DE VALIDACIÓN</th></tr>
        <tr>
          <th style="width:36px">#</th>
          <th style="width:110px">CÉDULA</th>
          <th style="width:80px">GRADO</th>
          <th>NOMBRES</th>
          <th>UNIDAD</th>
          <th>FUNCIÓN</th>
          <th style="width:120px">OBSERVACIÓN</th>
        </tr>
      </thead>
      <tbody>
        {$rowsN}
      </tbody>
    </table>

    {$firmasHTML($elab_nombre,$elab_grado,$elab_cargo,$rev_nombre,$rev_grado,$rev_cargo)}
  </main>
</body>
</html>
HTML;

        $filename = 'informe_' . now()->format('Ymd_His') . '.pdf';
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'portrait');
        return $pdf->download($filename);
    }
}
