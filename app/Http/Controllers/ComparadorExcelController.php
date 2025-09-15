<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ErroresComparacionExport;

/**
 * Filtro de lectura: solo columnas y filas indicadas
 */
class ColumnsFromRowFilter implements IReadFilter
{
    private int $startRow;
    /** @var string[] */
    private array $cols;

    public function __construct(int $startRow, array $cols)
    {
        $this->startRow = $startRow;
        $this->cols     = $cols;
    }

    public function readCell($column, $row, $sheet = ''): bool
    {
        return $row >= $this->startRow && in_array($column, $this->cols, true);
    }
}

class ComparadorExcelController extends Controller
{
    public function index()
    {
        $resultados = session('resultados');
        $resumen    = session('resumen');

        return view('comparador.index', compact('resultados', 'resumen'));
    }

    public function procesar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:256000', // 250 MB
        ]);

        if (!$request->hasFile('archivo')) {
            return back()->withErrors(['archivo' => 'No llegó ningún archivo (revisa post_max_size/upload_max_filesize).'])->withInput();
        }
        if (!$request->file('archivo')->isValid()) {
            return back()->withErrors(['archivo' => 'Error al subir: ' . $request->file('archivo')->getErrorMessage()])->withInput();
        }

        try {
            $file  = $request->file('archivo');
            $fpath = $file->getRealPath();

            // Lector: solo datos + filtrado de columnas C y G desde fila 20
            $reader = IOFactory::createReaderForFile($fpath);
            $reader->setReadDataOnly(true);
            $reader->setReadFilter(new ColumnsFromRowFilter(20, ['C','G']));

            $spreadsheet = $reader->load($fpath);
            $sheet       = $spreadsheet->getActiveSheet();

            $filaInicio = 20;
            $ultimaFila = (int) $sheet->getHighestDataRow();

            $errores = [];
            $ok      = 0;
            $saltos  = 0;

            for ($row = $filaInicio; $row <= $ultimaFila; $row++) {
                $cedulaExcel = $this->safeCellString($sheet, "C{$row}");
                $gExcelRaw   = $this->safeCellString($sheet, "G{$row}"); // ahora se compara contra NOMEN + FUNC

                $cedula   = $this->normalizarCedula($cedulaExcel);
                $gExcel   = $this->normalizarUnion($gExcelRaw); // normalizamos como "texto unión"

                // Fila vacía
                if ($cedula === '' && $gExcel === '') {
                    $saltos++;
                    continue;
                }

                // Busca cédula en BD con funcion_efectiva
                $usuario = DB::table('usuarios')
                    ->select('cedula', 'nomenclatura_efectiva', 'funcion_efectiva')
                    ->where('cedula', $cedula)
                    ->first();

                if (!$usuario) {
                    $errores[] = [
                        'fila_excel'         => $row,
                        'cedula_excel'       => $cedulaExcel,
                        'nomenclatura_excel' => $gExcelRaw, // G del Excel
                        'existe_en_bd'       => 'NO',
                        'nomenclatura_bd'    => '',         // unión no disponible
                        'motivo'             => 'Cédula no existe en la tabla usuarios',
                    ];
                    continue;
                }

                // Unión NOMEN + FUNC desde BD
                $unionBDraw = trim((string)($usuario->nomenclatura_efectiva ?? '') . ' ' . (string)($usuario->funcion_efectiva ?? ''));
                $unionBD    = $this->normalizarUnion($unionBDraw);

                if ($gExcel === '') {
                    $errores[] = [
                        'fila_excel'         => $row,
                        'cedula_excel'       => $cedulaExcel,
                        'nomenclatura_excel' => $gExcelRaw,
                        'existe_en_bd'       => 'SÍ',
                        'nomenclatura_bd'    => $unionBDraw,
                        'motivo'             => 'Columna G (Excel) vacía; se esperaba Nomenclatura + Función',
                    ];
                    continue;
                }

                if ($gExcel !== $unionBD) {
                    $errores[] = [
                        'fila_excel'         => $row,
                        'cedula_excel'       => $cedulaExcel,
                        'nomenclatura_excel' => $gExcelRaw,     // G del Excel
                        'existe_en_bd'       => 'SÍ',
                        'nomenclatura_bd'    => $unionBDraw,    // NOMEN + FUNC (BD)
                        'motivo'             => 'No coincide: (Excel G) vs (BD Nomenclatura + Función)',
                    ];
                    continue;
                }

                $ok++;
            }

            $resumen = [
                'fila_inicio'  => $filaInicio,
                'fila_fin'     => $ultimaFila,
                'filas_leidas' => max(0, $ultimaFila - $filaInicio + 1),
                'coinciden'    => $ok,
                'errores'      => count($errores),
                'saltos'       => $saltos,
            ];

            return redirect()
                ->route('comparador.index')
                ->with('resultados', $errores)
                ->with('resumen', $resumen);

        } catch (Throwable $e) {
            return back()->withErrors(['archivo' => 'No se pudo procesar el archivo: ' . $e->getMessage()])->withInput();
        }
    }

    public function exportar(Request $request)
    {
        $errores = session('resultados');
        if (!$errores || !is_array($errores) || count($errores) === 0) {
            return back()->withErrors(['exportar' => 'No hay datos para exportar. Primero procesa un archivo.']);
        }

        $export = new ErroresComparacionExport($errores);
        $nombre = 'informe_discrepancias_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download($export, $nombre);
    }

    // ================== Helpers ==================

    /** Lee como texto sin recalcular fórmulas. */
    private function safeCellString(Worksheet $sheet, string $addr): string
    {
        try {
            $v = $sheet->getCell($addr)->getValue();
        } catch (Throwable) {
            try { $v = $sheet->getCell($addr)->getOldCalculatedValue(); }
            catch (Throwable) { $v = ''; }
        }
        return trim((string) ($v ?? ''));
    }

    /** Normaliza cédula: sólo dígitos; si tiene 9, agrega 0 al inicio. */
    private function normalizarCedula(?string $v): string
    {
        if ($v === null) return '';
        $v = preg_replace('/\D+/', '', (string) $v);
        $v = trim($v ?? '');
        if (strlen($v) === 9) $v = '0' . $v;
        return $v;
    }

    /**
     * Normaliza textos de unión (para comparar "NOMEN + FUNC" con G).
     * - Mayúsculas
     * - Colapsa espacios
     * - Unifica guiones (— – − -> -) y quita espacios alrededor de guiones
     */
    private function normalizarUnion(?string $v): string
    {
        if ($v === null) return '';
        $v = strtoupper(trim($v));
        // unifica tipos de guión
        $v = preg_replace('/[–—−]/u', '-', $v);
        // quita espacios alrededor de guiones (ej: "NAP - DNATH" => "NAP-DNATH")
        $v = preg_replace('/\s*-\s*/', '-', $v);
        // colapsa espacios múltiples
        $v = preg_replace('/\s+/', ' ', $v);
        // opcional: quita guiones finales colgantes
        // $v = rtrim($v, '-');
        return $v;
    }
}
