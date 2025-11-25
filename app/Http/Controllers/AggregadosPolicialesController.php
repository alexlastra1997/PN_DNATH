<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AggregadosPolicialesController extends Controller
{
    public function index(Request $request)
    {
        // Funciones objetivo (coincidencia EXACTA con funcion_efectiva / cargo_organico)
        $funciones = [
            'ANALISTA DE COMISIONES AL EXTERIOR',
            'TRADUCTOR',
            'AGREGADO POLICIAL',
            'AYUDANTE ADMINISTRATIVO POLICIAL',
            'REPRESENTANTE POLICIAL EN EL EXTERIOR',
        ];

        // 1) Subconjunto del orgánico para funciones objetivo
        $organico = DB::table('reporte_organico as ro')
            ->select([
                'ro.servicio_organico',
                'ro.nomenclatura_organico',
                'ro.cargo_organico',
                DB::raw('COALESCE(ro.numero_organico_ideal, 0) as organico_aprobado'),
            ])
            ->whereIn('ro.cargo_organico', $funciones);

        // 2) Comparación exacta nomenclatura (orgánico vs efectiva) + función
        $comparado = DB::table(DB::raw("({$organico->toSql()}) as ro"))
            ->mergeBindings($organico)
            ->leftJoin('usuarios as u', function ($j) {
                $j->on('u.nomenclatura_efectiva', '=', 'ro.nomenclatura_organico')
                    ->on('u.funcion_efectiva', '=', 'ro.cargo_organico');
            })
            ->groupBy('ro.servicio_organico', 'ro.nomenclatura_organico', 'ro.cargo_organico', 'ro.organico_aprobado')
            ->select([
                'ro.servicio_organico',
                'ro.nomenclatura_organico',
                'ro.cargo_organico',
                'ro.organico_aprobado',
                DB::raw('COUNT(u.id) as organico_efectivo'),
            ])
            ->orderBy('ro.servicio_organico')
            ->orderBy('ro.nomenclatura_organico')
            ->orderBy('ro.cargo_organico')
            ->get()
            ->map(function ($row) {
                $ap = (int) $row->organico_aprobado;
                $ef = (int) $row->organico_efectivo;

                if ($ef === $ap)      $row->estado = 'COMPLETO';
                elseif ($ef < $ap)    $row->estado = 'VACANTE';
                else                  $row->estado = 'EXCEDENTE';

                // Detectar país desde la nomenclatura (y apoyos)
                $row->pais = $this->detectarPais(
                    $row->nomenclatura_organico.' '.$row->servicio_organico.' '.$row->cargo_organico
                );

                return $row;
            });

        // 3) Ocupantes por combinación (para modales)
        $ocupantes = [];
        foreach ($comparado as $row) {
            $key = md5($row->servicio_organico.'|'.$row->nomenclatura_organico.'|'.$row->cargo_organico);

            $ocupantes[$key] = DB::table('usuarios as u')
                ->select('u.id', 'u.cedula', 'u.apellidos_nombres', 'u.grado', 'u.nomenclatura_efectiva', 'u.funcion_efectiva', 'u.estado_efectivo')
                ->where('u.nomenclatura_efectiva', $row->nomenclatura_organico)
                ->where('u.funcion_efectiva', $row->cargo_organico)
                ->orderBy('u.grado')
                ->orderBy('u.apellidos_nombres')
                ->get();
        }

        // 4) Usuarios con funciones objetivo pero SIN combinación exacta en el orgánico
        $usuariosFunciones = DB::table('usuarios as u')
            ->select('u.id', 'u.cedula', 'u.apellidos_nombres', 'u.grado', 'u.nomenclatura_efectiva', 'u.funcion_efectiva', 'u.estado_efectivo')
            ->whereIn('u.funcion_efectiva', $funciones)
            ->get();

        $combosOrganico = DB::table('reporte_organico as ro')
            ->select('ro.nomenclatura_organico', 'ro.cargo_organico')
            ->whereIn('ro.cargo_organico', $funciones)
            ->get()
            ->map(fn($r) => $r->nomenclatura_organico.'|'.$r->cargo_organico)
            ->unique()
            ->flip();

        $desalineados = $usuariosFunciones->filter(function ($u) use ($combosOrganico) {
            return !$combosOrganico->has($u->nomenclatura_efectiva.'|'.$u->funcion_efectiva);
        })->values();

        // 5) Agrupar por país para tabs
        $porPais = [];
        foreach ($comparado as $row) {
            $pais = $row->pais ?: 'SIN PAÍS';
            if (!isset($porPais[$pais])) $porPais[$pais] = [];
            $porPais[$pais][] = $row;
        }

        // Orden alfabético de pestañas
        ksort($porPais, SORT_NATURAL | SORT_FLAG_CASE);

        return view('agregados_policiales', [
            'porPais'      => $porPais,       // [pais => [rows...]]
            'comparado'    => $comparado,     // por si lo necesitas
            'ocupantes'    => $ocupantes,     // clave => lista
            'desalineados' => $desalineados,
        ]);
    }

    /**
     * Detecta país desde un texto (nomenclatura/servicio/cargo).
     * Ajusta/añade alias según tus nomenclaturas reales.
     */
    private function detectarPais(string $texto): ?string
    {
        $t = mb_strtoupper($texto, 'UTF-8');

        // Alias y variantes comunes en tus nomenclaturas
        $map = [
            'EE UU' => ['EE UU', 'EEUU', 'ESTADOS UNIDOS', 'U.S.A', 'USA', 'EE. UU.'],
            'ESPAÑA' => ['ESPAÑA', 'ESPANA'],
            'FRANCIA' => ['FRANCIA'],
            'ALEMANIA' => ['ALEMANIA', 'GERMANIA'],
            'PERÚ' => ['PERU', 'PERÚ'],
            'PANAMÁ' => ['PANAMA', 'PANAMÁ'],
            'MÉXICO' => ['MEXICO', 'MÉXICO'],
            'COLOMBIA' => ['COLOMBIA'],
            'CHILE' => ['CHILE'],
            'ARGENTINA' => ['ARGENTINA'],
            'ECUADOR' => ['ECUADOR'],
            'AMERIPOL' => ['AMERIPOL'], // organismo; lo tratamos como "país" pestaña propia
            // agrega más si los usas: ITALIA, CANADÁ, BRASIL, PARAGUAY, etc.
        ];

        foreach ($map as $pais => $variantes) {
            foreach ($variantes as $v) {
                if (str_contains($t, mb_strtoupper($v, 'UTF-8'))) {
                    return $pais;
                }
            }
        }

        // Heurística simple: si el texto tiene "AG <PAIS>" como patrón
        if (preg_match('/\bAG\s+([A-ZÁÉÍÓÚÜÑ\. ]{2,})\b/u', $t, $m)) {
            $posible = trim($m[1]);
            // limpieza rápida de guiones/códigos
            $posible = preg_replace('/[^A-ZÁÉÍÓÚÜÑ ]/u', '', $posible);
            $posible = preg_replace('/\s+/', ' ', $posible);
            return $posible;
        }

        return null;
    }
}
