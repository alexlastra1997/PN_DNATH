<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ServidoresImport;
use App\Exports\TruequeMultipleExport;
use Illuminate\Support\Facades\Storage;

class TruequeController extends Controller
{
    public function index()
    {
        return view('trueque.index');
    }

    public function procesar(Request $request)
    {
        $request->validate([ 
            'archivo1' => 'required|file|mimes:xlsx,xls',
            'archivo2' => 'required|file|mimes:xlsx,xls',
        ]);

        $carcel = Excel::toArray(new ServidoresImport, $request->file('archivo1'))[0];
        $libres = Excel::toArray(new ServidoresImport, $request->file('archivo2'))[0];

        $header = array_shift($carcel);
        array_shift($libres);

        $carcel = array_map(fn($fila) => array_combine($header, $fila), $carcel);
        $libres = array_map(fn($fila) => array_combine($header, $fila), $libres);

        $provincia_permitida = [
            'NAPO' => ['SUCUMBIOS', 'PASTAZA', 'MORONA SANTIAGO', 'ORELLANA',' ', 'ECUADOR','PICHINCHA', 'IMBABURA'],
            'MANABI' => ['MANABI','GUAYAS','PICHINCHA',],
            'STO DOMINGO TSACHILAS' => ['STO DOMINGO TSACHILAS', 'ESMERALDAS', 'PICHINCHA',' ', 'ECUADOR','PICHINCHA', 'IMBABURA'],
            'LOS RIOS' => ['LOS RIOS', 'SANTA ELENA', 'BOLIVAR', 'CAÑAR',' ', 'ECUADOR','PICHINCHA',],
            'LOJA' => ['LOJA',' ', 'ECUADOR','AZUAY','PICHINCHA',],
            'COTOPAXI' => ['PICHINCHA', 'TUNGURAHUA', 'CARCHI', 'CHIMBORAZO', 'IMBABURA', 'COTOPAXI',' ', 'ECUADOR'],
            'AZUAY' => ['AZUAY', 'BOLIVAR', 'LOJA', 'CAÑAR', 'ZAMORA CHINCHIPE',' ', 'ECUADOR','PICHINCHA',],
            'GUAYAS' => ['GUAYAS', 'LOS RIOS',' ', 'ECUADOR','AZUAY','BOLIVAR', 'CHIMBORAZO','SANTA ELENA','PICHINCHA',],
        ];

        $trueques = [];
        $rezagados = [];
        $usados_libres = [];

        foreach ($carcel as &$p1) {
            $prov_destino = strtoupper(trim($p1['provincia_vive']));
            $grado = strtoupper(trim($p1['grado']));
            $match = false;

            foreach ($libres as $k => $p2) {
                if (in_array($p2['provincia_vive'], $provincia_permitida[$prov_destino] ?? []) && strtoupper($p2['grado']) == $grado && !in_array($p2['cedula'], $usados_libres)) {
                    $p1['nueva_nomenclatura'] = $p2['nomenclatura'];
                    $p1['nueva_funcion'] = $p2['funcion'];
                    $p1['cedula_truequeado_con'] = $p2['cedula'];

                    $libres[$k]['nueva_nomenclatura'] = $p1['nomenclatura'];
                    $libres[$k]['nueva_funcion'] = $p1['funcion'];
                    $libres[$k]['cedula_truequeado_con'] = $p1['cedula'];

                    $trueques[] = $p1;
                    $trueques[] = $libres[$k];

                    $usados_libres[] = $p2['cedula'];
                    $match = true;
                    break;
                }
            }

            if (!$match) {
                $p1['nueva_nomenclatura'] = '';
                $p1['nueva_funcion'] = '';
                $p1['cedula_truequeado_con'] = '';
                $rezagados[] = $p1;
            }
        }

        foreach ($libres as $p2) {
            if (!in_array($p2['cedula'], $usados_libres)) {
                $p2['nueva_nomenclatura'] = '';
                $p2['nueva_funcion'] = '';
                $p2['cedula_truequeado_con'] = '';
                $rezagados[] = $p2;
            }
        }

        $export = new TruequeMultipleExport($trueques, $rezagados);
        $fileName = 'trueque_carceles.xlsx';
        Excel::store($export, $fileName, 'local');

        return response()->download(storage_path("app/{$fileName}"))->deleteFileAfterSend(true);
    }
}
