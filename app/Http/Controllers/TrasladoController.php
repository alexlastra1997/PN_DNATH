<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Exports\ArrayExport;
use ZipArchive;

class TrasladoController extends Controller
{
    public function index()
    {
        return view('traslados.index');
    }

    public function procesar(Request $request)

    {
        $request->validate([
            'archivo1' => 'required|file|mimes:xlsx,xls',
            'archivo2' => 'required|file|mimes:xlsx,xls',
            'titulo' => 'required|string|max:100'
        ]);

        $titulo = preg_replace('/[^A-Za-z0-9 _-]/', '', $request->input('titulo'));
        $titulo = str_replace(' ', '_', $titulo);

        $archivo1 = $request->file('archivo1');
        $archivo2 = $request->file('archivo2');

        $data1 = Excel::toArray([], $archivo1)[0];
        $data2 = Excel::toArray([], $archivo2)[0];

        $headers = $data1[0];
        unset($data1[0], $data2[0]);

        $data1 = array_values($data1);
        $data2 = array_values($data2);

        $modificados1 = [];
        $modificados2 = [];

        $min = min(count($data1), count($data2));

        for ($i = 0; $i < $min; $i++) {
            $u1 = array_combine($headers, $data1[$i]);
            $u2 = array_combine($headers, $data2[$i]);

            $modificados1[] = [
                'cedula' => $u1['cedula'] ?? '',
                'grado' => $u1['grado'] ?? '',
                'nombres' => $u1['nombres'] ?? $u1['apellidos_nombres'] ?? '',
                'id_unidad_real' => $u1['id_unidad_real'],
                'id_funcion_real' => $u1['id_funcion_real'],
                'unidad_real' => $u1['unidad_real'],
                'funcion_real' => $u1['funcion_real'],
                'id_unidad_destino' => $u2['id_unidad_real'],
                'id_funcion_destino' => $u2['id_funcion_real'],
                'unidad_destino' => $u2['unidad_real'],
                'funcion_destino' => $u2['funcion_real']
            ];

            $modificados2[] = [
                'cedula' => $u2['cedula'] ?? '',
                'grado' => $u2['grado'] ?? '',
                'nombres' => $u2['nombres'] ?? $u2['apellidos_nombres'] ?? '',
                'id_unidad_real' => $u2['id_unidad_real'],
                'id_funcion_real' => $u2['id_funcion_real'],
                'unidad_real' => $u2['unidad_real'],
                'funcion_real' => $u2['funcion_real'],
                'id_unidad_destino' => $u1['id_unidad_real'],
                'id_funcion_destino' => $u1['id_funcion_real'],
                'unidad_destino' => $u1['unidad_real'],
                'funcion_destino' => $u1['funcion_real']
            ];
        }

        $folder = 'traslados/' . Str::uuid();
        Storage::makeDirectory($folder);

        $archivoMod1 = "$folder/{$titulo}1.xlsx";
        $archivoMod2 = "$folder/{$titulo}2.xlsx";
        $archivoPDF = "$folder/{$titulo}.pdf";

        Excel::store(new ArrayExport($modificados1), $archivoMod1, 'local');
        Excel::store(new ArrayExport($modificados2), $archivoMod2, 'local');

        $pdf = Pdf::loadView('traslados.resumen_pdf', ['datos1' => $modificados1]);
        Storage::put($archivoPDF, $pdf->output());

        $zipPath = storage_path("app/{$folder}/resultado_final.zip");
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile(storage_path("app/{$archivoMod1}"), basename($archivoMod1));
            $zip->addFile(storage_path("app/{$archivoMod2}"), basename($archivoMod2));
            $zip->addFile(storage_path("app/{$archivoPDF}"), basename($archivoPDF));
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
