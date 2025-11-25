<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteOrganicoExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    protected Collection $rows;

    /** filas (números) de la hoja que deben sombrearse en plomo (TODAS las de contexto) */
    protected array $shadeRowIndexes = [];

    public function __construct(Collection $rows)
    {
        $this->rows = $rows->values();
    }

    /** Fila 1: títulos (A..F) */
    public function headings(): array
    {
        return [
            'Servicio',
            'Nomenclatura',
            'Cargo',
            'Subsistema',
            'Aprobado',
            'Efectivo',
        ];
    }

    /** Títulos en negrita + fondo celeste */
    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:F1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD9EDF7'); // celeste
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        return [1 => ['font' => ['bold' => true]]];
    }

    /**
     * Datos:
     *  - por cargo, 1 fila de contexto en A..F (se marca SIEMPRE para sombrear)
     *  - debajo, cédula/grado/nombre en A..C, tantas filas como max(Aprobado, #ocupantes)
     */
    public function collection(): Collection
    {
        $out    = collect();
        $rojas  = ['202344', '202506', '202345']; // promos para cédula roja
        $rowNum = 2; // la fila 1 son los headings

        foreach ($this->rows as $row) {
            // --- Fila de contexto (A..F)
            $out->push([
                $row->Servicio,
                $row->Nomenclatura,
                $row->Cargo,
                $row->Subsistema,
                (int)($row->Aprobado ?? 0),
                (int)($row->Efectivo ?? 0),
            ]);

            // Marcar SIEMPRE esta fila de contexto para fondo plomo
            $this->shadeRowIndexes[] = $rowNum;
            $rowNum++;

            // Ocupantes (exactos + heredados si es cabecera)
            $exact = !empty($row->OcupantesExactJSON) ? json_decode($row->OcupantesExactJSON, true) : [];
            $base  = ((int)($row->EsCabecera ?? 0) === 1 && !empty($row->OcupantesBaseJSON))
                ? json_decode($row->OcupantesBaseJSON, true) : [];
            $todos = array_values(array_merge($exact, $base));
            $total = count($todos);

            $aprobado = (int)($row->Aprobado ?? 0);
            $slots    = max($aprobado, $total);

            // --- Filas verticales: A=Cédula, B=Grado, C=Nombre
            for ($i = 0; $i < $slots; $i++, $rowNum++) {
                $cedulaCell = '';
                $gradoCell  = '';
                $nombreCell = '';

                if (isset($todos[$i])) {
                    $cedula = trim((string)($todos[$i]['c'] ?? ''));
                    $grado  = trim((string)($todos[$i]['g'] ?? ''));
                    $nombre = trim((string)($todos[$i]['n'] ?? ''));
                    $promo  = (string)($todos[$i]['p'] ?? '');

                    if ($cedula !== '') {
                        $rt  = new RichText();
                        $run = $rt->createTextRun($cedula);
                        if (in_array($promo, $rojas, true)) {
                            $run->getFont()->getColor()->setARGB(Color::COLOR_RED);
                        }
                        $cedulaCell = $rt;     // A
                    }
                    $gradoCell  = $grado;    // B
                    $nombreCell = $nombre;   // C
                }

                // Fila ocupante: A..C
                $out->push([$cedulaCell, $gradoCell, $nombreCell]);
            }
        }

        return $out->values();
    }

    /** Sombrear en plomo TODAS las filas de contexto recopiladas */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                foreach ($this->shadeRowIndexes as $r) {
                    $sheet->getStyle("A{$r}:F{$r}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFD9D9D9'); // gris/plomo
                }
            },
        ];
    }
}
