<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NomenclaturaController extends Controller
{
    public function nomenclatura(Request $request, $niveles = null)
    {
        // Aseguramos que niveles sea siempre array
        $niveles = $niveles ? explode('/', urldecode($niveles)) : [];

        $nivelActual = count($niveles);

        // Leer todos los registros
        $registros = DB::table('reporte_organico')->get();

        $proximosNiveles = [];
        $registrosFinales = [];

        foreach ($registros as $registro) {
            $partes = explode('-', trim($registro->nomenclatura, '-'));

            // Comparar niveles navegados
            $coincide = true;
            foreach ($niveles as $index => $nivelEsperado) {
                if (!isset($partes[$index]) || $partes[$index] !== $nivelEsperado) {
                    $coincide = false;
                    break;
                }
            }

            if (!$coincide) {
                continue;
            }

            // Si hay siguiente nivel, seguimos
            if (isset($partes[$nivelActual])) {
                $proximosNiveles[] = $partes[$nivelActual];
            } else {
                // No hay más niveles -> registro final
                $registrosFinales[] = $registro;
            }
        }

        $nombresCards = array_count_values($proximosNiveles);

        return view('organico_nomenclatura', [
            'nombresCards' => $nombresCards,
            'niveles' => $niveles,
            'registrosFinales' => $registrosFinales
        ]);
    }
}
