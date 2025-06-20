<?php

namespace App\Http\Controllers;

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

    $cambios1 = [];
    $cambios2 = [];

    for ($i = 0; $i < $min; $i++) {
        $unidad1 = $data1[$i]['D'] ?? '';
        $unidad2 = $data2[$i]['D'] ?? '';

        // Intercambio
        $data1[$i]['D'] = $unidad2;
        $data2[$i]['D'] = $unidad1;

        // Registrar cambio
        $data1[$i]['E'] = $unidad1 . ' → ' . $unidad2;
        $data2[$i]['E'] = $unidad2 . ' ← ' . $unidad1;

        // Marcar cambio
        $data1[$i]['CAMBIO'] = ($unidad1 !== $unidad2);
        $data2[$i]['CAMBIO'] = ($unidad1 !== $unidad2);

        if ($unidad1 !== $unidad2) {
            $cambios1[] = $data1[$i];
            $cambios2[] = $data2[$i];
        }
    }

    // Agregar encabezado columna E
    $header1['E'] = 'CAMBIO DE UNIDAD';
    $header2['E'] = 'CAMBIO DE UNIDAD';

    // Excel 1
    $spreadsheetOut1 = new Spreadsheet();
    $sheetOut1 = $spreadsheetOut1->getActiveSheet();
    $sheetOut1->fromArray(array_values($header1), null, 'A1');

    // Excel 2
    $spreadsheetOut2 = new Spreadsheet();
    $sheetOut2 = $spreadsheetOut2->getActiveSheet();
    $sheetOut2->fromArray(array_values($header2), null, 'A1');

    // Rellenar datos
    $row1 = 2;
    foreach ($data1 as $fila) {
        $sheetOut1->fromArray(array_values($fila), null, 'A' . $row1);
        if (!empty($fila['CAMBIO'])) {
            $sheetOut1->getStyle("A$row1:E$row1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('C6EFCE');
        }
        $row1++;
    }

    $row2 = 2;
    foreach ($data2 as $fila) {
        $sheetOut2->fromArray(array_values($fila), null, 'A' . $row2);
        if (!empty($fila['CAMBIO'])) {
            $sheetOut2->getStyle("A$row2:E$row2")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFEB9C');
        }
        $row2++;
    }

    // Guardar archivos
    $file1 = tempnam(sys_get_temp_dir(), 'cambios1_') . '.xlsx';
    $file2 = tempnam(sys_get_temp_dir(), 'cambios2_') . '.xlsx';

    IOFactory::createWriter($spreadsheetOut1, 'Xlsx')->save($file1);
    IOFactory::createWriter($spreadsheetOut2, 'Xlsx')->save($file2);

    // Crear ZIP
    $zipFile = tempnam(sys_get_temp_dir(), 'traslados_') . '.zip';
    $zip = new \ZipArchive();
    $zip->open($zipFile, \ZipArchive::CREATE);
    $zip->addFile($file1, 'cambios_archivo1.xlsx');
    $zip->addFile($file2, 'cambios_archivo2.xlsx');
    $zip->close();

    return response()->download($zipFile, 'traslados_resultado.zip')->deleteFileAfterSend(true);
}
}