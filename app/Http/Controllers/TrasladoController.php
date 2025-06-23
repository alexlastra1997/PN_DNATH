<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TrasladoController extends Controller
{
    public function index()
    {
        return view('traslados.index');
    }

 public function procesar(Request $request)
    {
        $spreadsheet1 = IOFactory::load($request->file('archivo1'));
        $spreadsheet2 = IOFactory::load($request->file('archivo2'));

        $sheet1 = $spreadsheet1->getActiveSheet();
        $sheet2 = $spreadsheet2->getActiveSheet();

        $data1 = $sheet1->toArray(null, true, true, true);
        $data2 = $sheet2->toArray(null, true, true, true);

        $header1 = array_shift($data1);
        $header2 = array_shift($data2);

        $min = min(count($data1), count($data2));
        shuffle($data2);

        for ($i = 0; $i < $min; $i++) {
            $unidad1 = $data1[$i]['D'] ?? '';
            $unidad2 = $data2[$i]['D'] ?? '';

            $data1[$i]['E'] = $unidad1;
            $data1[$i]['F'] = $unidad1 . ' → ' . $unidad2;
            $data1[$i]['G'] = $unidad2;

            $data2[$i]['E'] = $unidad2;
            $data2[$i]['F'] = $unidad2 . ' → ' . $unidad1;
            $data2[$i]['G'] = $unidad1;

            $data1[$i]['D'] = $unidad2;
            $data2[$i]['D'] = $unidad1;
        }

        $header1['E'] = 'UNIDAD ORIGEN';
        $header1['F'] = 'CAMBIO UNIDAD';
        $header1['G'] = 'UNIDAD DESTINO';

        $header2['E'] = 'UNIDAD ORIGEN';
        $header2['F'] = 'CAMBIO UNIDAD';
        $header2['G'] = 'UNIDAD DESTINO';

        $spreadsheetOut1 = new Spreadsheet();
        $sheetOut1 = $spreadsheetOut1->getActiveSheet();
        $sheetOut1->fromArray(array_values($header1), null, 'A1');

        $row1 = 2;
        foreach ($data1 as $fila) {
            $sheetOut1->fromArray(array_values($fila), null, 'A' . $row1);

            if ($color = $this->getZonaColor($fila['E'] ?? '')) {
                $sheetOut1->getStyle("E$row1")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
            }

            if ($color = $this->getZonaColor($fila['G'] ?? '')) {
                $sheetOut1->getStyle("G$row1")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
            }

            $row1++;
        }

        $spreadsheetOut2 = new Spreadsheet();
        $sheetOut2 = $spreadsheetOut2->getActiveSheet();
        $sheetOut2->fromArray(array_values($header2), null, 'A1');

        $row2 = 2;
        foreach ($data2 as $fila) {
            $sheetOut2->fromArray(array_values($fila), null, 'A' . $row2);

            if ($color = $this->getZonaColor($fila['E'] ?? '')) {
                $sheetOut2->getStyle("E$row2")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
            }

            if ($color = $this->getZonaColor($fila['G'] ?? '')) {
                $sheetOut2->getStyle("G$row2")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
            }

            $row2++;
        }

        $file1 = tempnam(sys_get_temp_dir(), 'cambios1_') . '.xlsx';
        $file2 = tempnam(sys_get_temp_dir(), 'cambios2_') . '.xlsx';

        IOFactory::createWriter($spreadsheetOut1, 'Xlsx')->save($file1);
        IOFactory::createWriter($spreadsheetOut2, 'Xlsx')->save($file2);

        // Generar PDF resumen
        $headers = array_values($header1);
        $listado1 = array_map(fn($fila) => array_values($fila), $data1);
        $listado2 = array_map(fn($fila) => array_values($fila), $data2);

        $pdf = Pdf::loadView('pdf.traslados', compact('headers', 'listado1', 'listado2'));
        $pdfFile = tempnam(sys_get_temp_dir(), 'traslados_pdf_') . '.pdf';
        file_put_contents($pdfFile, $pdf->output());

        // Crear ZIP con Excel y PDF
        $zipFile = tempnam(sys_get_temp_dir(), 'traslados_') . '.zip';
        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::CREATE);
        $zip->addFile($file1, 'cambios_archivo1.xlsx');
        $zip->addFile($file2, 'cambios_archivo2.xlsx');
        $zip->addFile($pdfFile, 'traslados_resumen.pdf');
        $zip->close();

        return response()->download($zipFile, 'traslados_resultado.zip')->deleteFileAfterSend(true);
    }

    private function getZonaColor(string $unidad): ?string
    {
        $unidad = strtoupper($unidad);

        return match (true) {
            str_contains($unidad, 'Z1')      => 'FFF3CD',
            str_contains($unidad, 'Z2')      => 'F8D7DA',
            str_contains($unidad, 'Z3')      => 'D6D8D9',
            str_contains($unidad, 'Z4')  => 'D4EDDA',
            str_contains($unidad, 'Z5')  => 'E8DAEF',
            str_contains($unidad, 'Z6')  => 'FADBD8',
            str_contains($unidad, 'Z7')  => 'E5D6C3',
            str_contains($unidad, 'Z8')  => 'D1ECF1',
            str_contains($unidad, 'Z9')  => 'FFF9E6',
            default                          => null,
        };
    }

}