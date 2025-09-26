<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class UsuariosImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, SkipsEmptyRows
{
    /** @var array<string> */
    protected array $columns;

    /** @var int */
    protected int $ok = 0;

    /** @var array<int, array{fila:int, cedula?:string, motivo:string}> */
    protected array $errores = [];

    /** @var int */
    protected int $filaActual = 1; // HeadingRow = 1

    public function __construct()
    {
        // Solo mapear a columnas realmente existentes en la tabla 'usuarios'
        $this->columns = Schema::getColumnListing('usuarios');
    }

    /** Compatibilidad con tu método previo */
    protected function convertirFecha($valor)
    {
        try {
            if (is_numeric($valor)) {
                // Excel 1900 system; ajustar -2 por bug del leap-year
                return Carbon::createFromDate(1900, 1, 1)->addDays($valor - 2)->format('Y-m-d');
            }
            return Carbon::parse($valor)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /** Convierte a fecha Y-m-d cualquier campo cuyo nombre contenga "fecha" */
    protected function toDate($valor): ?string
    {
        if ($valor === null || $valor === '') return null;
        try {
            if (is_numeric($valor)) {
                return Carbon::createFromDate(1900, 1, 1)->addDays(((int)$valor) - 2)->format('Y-m-d');
            }
            return Carbon::parse((string)$valor)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Normaliza encabezados: tildes → ASCII, snake_case y limpieza */
    protected function normKey(string $k): string
    {
        $k = Str::of($k)
            ->ascii()
            ->lower()
            ->replace(['#', '%', '/', '\\'], ' ')
            ->replace(['(', ')', '.', ',', ';', ':', '[', ']', '{', '}', '"', '\''], '')
            ->replace(['-'], ' ')
            ->squish()
            ->replace(' ', '_')
            ->value();

        // Alias comunes; añade los tuyos si hace falta
        $aliases = [
            'cedula_'                => 'cedula',
            'cédula'                 => 'cedula',
            'apellidos_y_nombres'    => 'apellidos_nombres',
            'apellidos_nombres_'     => 'apellidos_nombres',
            'provincia_trabaja_'     => 'provincia_trabaja',
            'funcion_efectiva_'      => 'funcion_efectiva',
            'nomenclatura_efectiva_' => 'nomenclatura_efectiva',
            'fechapresentacion'      => 'fechaPresentacion',
            'numerodias'             => 'numeroDias',
        ];
        return $aliases[$k] ?? $k;
    }

    /** Aplica normalización de encabezados a toda la fila */
    protected function normRow(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            $out[$this->normKey((string)$k)] = is_string($v) ? trim($v) : $v;
        }
        return $out;
    }

    /** Normaliza cédula: solo dígitos y rellena a 10 */
    protected function normCedula(?string $c): ?string
    {
        if ($c === null) return null;
        $digits = preg_replace('/\D+/', '', $c) ?? '';
        if ($digits === '') return null;
        return str_pad($digits, 10, '0', STR_PAD_LEFT);
    }

    public function model(array $row)
    {
        $this->filaActual++;

        // 1) Normaliza encabezados/valores
        $r = $this->normRow($row);

        // 2) Clave obligatoria: cédula
        $ced = $this->normCedula($r['cedula'] ?? null);
        if (!$ced) {
            $this->errores[] = ['fila' => $this->filaActual, 'motivo' => 'Falta cédula o inválida'];
            return null;
        }

        // 3) Construye payload solo con columnas existentes
        $data = ['cedula' => $ced];

        foreach ($this->columns as $col) {
            if (in_array($col, ['id', 'cedula', 'created_at', 'updated_at'], true)) {
                continue;
            }
            if (!array_key_exists($col, $r)) {
                continue;
            }

            $val = $r[$col];

            // Convertir automáticamente cualquier campo que contenga "fecha"
            if (stripos($col, 'fecha') !== false) {
                $val = $this->toDate($val);
            }

            $data[$col] = $val;
        }

        try {
            DB::table('usuarios')->updateOrInsert(['cedula' => $ced], $data);
            $this->ok++;
            Log::info("✔️ Importado: {$ced}");
        } catch (\Throwable $e) {
            $this->errores[] = [
                'fila'   => $this->filaActual,
                'cedula' => $ced,
                'motivo' => $e->getMessage(),
            ];
            Log::error("❌ Error con {$ced}: " . $e->getMessage());
        }

        return null;
    }

    /** Tamaño de chunk para archivos grandes */
    public function chunkSize(): int
    {
        return 1000;
    }

    /** Inserciones por batch */
    public function batchSize(): int
    {
        return 1000;
    }

    /** Resumen para consultar desde el controlador (opcional) */
    public function resumen(): array
    {
        return [
            'insertados_actualizados' => $this->ok,
            'errores'                 => $this->errores,
        ];
    }
}
