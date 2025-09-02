<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ResumenOrganicoExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return ['Nivel', 'Aprobado', 'Efectivo', 'Traslado Temporal', 'Traslado Eventual', 'Unidad de Origen'];
    }

    public function array(): array
    {
        $req = (object) $this->filters;

        $base = DB::table('reporte_organico as ro')
            ->when(!empty($req->servicio), fn($q) => $q->where('ro.servicio_organico', 'like', '%'.$req->servicio.'%'))
            ->when(!empty($req->nomenclatura), fn($q) => $q->where('ro.nomenclatura_organico', 'like', '%'.$req->nomenclatura.'%'))
            ->when(!empty($req->cargo), fn($q) => $q->where('ro.cargo_organico', 'like', '%'.$req->cargo.'%'));

        if (!empty($req->estado)) {
            $ef = DB::raw('(SELECT COUNT(*) FROM usuarios u
                            WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                              AND u.funcion_efectiva     = ro.cargo_organico)');
            if ($req->estado === 'VACANTE')  $base->whereRaw("$ef < ro.numero_organico_ideal");
            if ($req->estado === 'COMPLETO') $base->whereRaw("$ef = ro.numero_organico_ideal");
            if ($req->estado === 'EXCEDIDO') $base->whereRaw("$ef > ro.numero_organico_ideal");
        }

        $sumAp = 'COALESCE(SUM(ro.numero_organico_ideal),0) as total_aprobado';
        $sumEf = 'COALESCE(SUM((SELECT COUNT(*) FROM usuarios u
                                 WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico
                                   AND u.funcion_efectiva     = ro.cargo_organico)),0) as total_efectivo';

        $totales             = (clone $base)->selectRaw($sumAp)->selectRaw($sumEf)->first();
        $nivelAsesor         = (clone $base)->whereRaw("UPPER(ro.nomenclatura_organico) LIKE 'NAS%'")
            ->whereRaw("UPPER(ro.nomenclatura_organico) NOT LIKE 'DINASED%'")
            ->selectRaw($sumAp)->selectRaw($sumEf)->first();
        $nivelApoyo          = (clone $base)->whereRaw("UPPER(ro.nomenclatura_organico) LIKE 'NAP%'")
            ->whereRaw("UPPER(ro.nomenclatura_organico) NOT LIKE 'NAPO%'")
            ->selectRaw($sumAp)->selectRaw($sumEf)->first();
        $nivelCoordinacion   = (clone $base)->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NCOORD%'")
            ->selectRaw($sumAp)->selectRaw($sumEf)->first();
        $nivelDesconcentrado = (clone $base)->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NDESC%'")
            ->selectRaw($sumAp)->selectRaw($sumEf)->first();

        $ndescZonal    = (clone $base)->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NDESC%'")
            ->whereRaw("UPPER(ro.servicio_organico) LIKE '%PREV-ZONAL%'")
            ->selectRaw($sumAp)->selectRaw($sumEf)->first();
        $ndescSubzonal = (clone $base)->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NDESC%'")
            ->whereRaw("UPPER(ro.servicio_organico) LIKE '%PREV-SZ%'")
            ->selectRaw($sumAp)->selectRaw($sumEf)->first();
        $ndescDCS      = (clone $base)->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NDESC%'")
            ->whereRaw("UPPER(ro.servicio_organico) LIKE '%PREV-D-C-S%'")
            ->selectRaw($sumAp)->selectRaw($sumEf)->first();

        $jefPrev        = (clone $base)->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%JPREV%'")->selectRaw($sumAp)->selectRaw($sumEf)->first();
        $jefInv         = (clone $base)->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%JINV%'")->selectRaw($sumAp)->selectRaw($sumEf)->first();
        $jefInt         = (clone $base)->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%JINT%'")->selectRaw($sumAp)->selectRaw($sumEf)->first();
        $nivelDirectivo = (clone $base)->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NDIREC%'")->selectRaw($sumAp)->selectRaw($sumEf)->first();
        $nivelOperativo = (clone $base)->whereRaw("UPPER(ro.nomenclatura_organico) LIKE '%NOPERA%'")->selectRaw($sumAp)->selectRaw($sumEf)->first();

        $estadoEfectivo = (clone $base)
            ->join('usuarios as u', function ($j) {
                $j->on('u.nomenclatura_efectiva','=','ro.nomenclatura_organico')
                    ->on('u.funcion_efectiva','=','ro.cargo_organico');
            })
            ->whereNotNull('u.estado_efectivo')
            ->where(function ($w) {
                $w->whereRaw("UPPER(u.estado_efectivo) LIKE '%TRASLADO%TEMPORAL%'")
                    ->orWhereRaw("UPPER(u.estado_efectivo) LIKE '%TRASLADO%EVENTUAL%'")
                    ->orWhereRaw("UPPER(u.estado_efectivo) LIKE '%UNIDAD%DE%ORIGEN%'");
                // ->orWhereRaw("UPPER(u.estado_efectivo) LIKE '%TRASLADO%OCASIONAL%'");
            })
            ->selectRaw("CASE
        WHEN UPPER(u.estado_efectivo) LIKE '%TRASLADO%TEMPORAL%' THEN 'TT'
        WHEN UPPER(u.estado_efectivo) LIKE '%TRASLADO%EVENTUAL%' THEN 'TE'
        WHEN UPPER(u.estado_efectivo) LIKE '%UNIDAD%DE%ORIGEN%'  THEN 'UO'
    END as cat")
            ->selectRaw('COUNT(DISTINCT u.id) as total')
            ->groupBy('cat')
            ->pluck('total','cat')
            ->all();


        $tt = (int)($estadoEfectivo['TT'] ?? 0);
        $te = (int)($estadoEfectivo['TE'] ?? 0);
        $uo = (int)($estadoEfectivo['UO'] ?? 0);

        $val = fn($o,$f) => (int)($o->{$f} ?? 0);

        $rows = [];
        $rows[] = ['Orgánico Aprobado (Total)', $val($totales,'total_aprobado'), '', '', '', ''];
        $rows[] = ['Orgánico Efectivo (Total)', '', $val($totales,'total_efectivo'), $tt, $te, $uo];

        $rows[] = ['Nivel Asesor (NAS)', $val($nivelAsesor,'total_aprobado'), $val($nivelAsesor,'total_efectivo'), '', '', ''];
        $rows[] = ['Nivel de Apoyo (NAP)', $val($nivelApoyo,'total_aprobado'), $val($nivelApoyo,'total_efectivo'), '', '', ''];
        $rows[] = ['Nivel de Coordinación (NCOORD)', $val($nivelCoordinacion,'total_aprobado'), $val($nivelCoordinacion,'total_efectivo'), '', '', ''];

        $rows[] = ['Nivel Desconcentrado (NDESC) — Total', $val($nivelDesconcentrado,'total_aprobado'), $val($nivelDesconcentrado,'total_efectivo'), '', '', ''];
        $rows[] = ['  ZONAL (PREV-ZONAL)', $val($ndescZonal,'total_aprobado'), $val($ndescZonal,'total_efectivo'), '', '', ''];
        $rows[] = ['  SUBZONAL (PREV-SZ)', $val($ndescSubzonal,'total_aprobado'), $val($ndescSubzonal,'total_efectivo'), '', '', ''];
        $rows[] = ['  DISTRITO – CIRCUITO – SUBCIRCUITO (PREV-D-C-S)', $val($ndescDCS,'total_aprobado'), $val($ndescDCS,'total_efectivo'), '', '', ''];

        $rows[] = ['Jefatura Preventiva (JPREV)', $val($jefPrev,'total_aprobado'), $val($jefPrev,'total_efectivo'), '', '', ''];
        $rows[] = ['Jefatura de Investigación (JINV)', $val($jefInv,'total_aprobado'), $val($jefInv,'total_efectivo'), '', '', ''];
        $rows[] = ['Jefatura de Inteligencia (JINT)', $val($jefInt,'total_aprobado'), $val($jefInt,'total_efectivo'), '', '', ''];

        $rows[] = ['Nivel Directivo (NDIREC)', $val($nivelDirectivo,'total_aprobado'), $val($nivelDirectivo,'total_efectivo'), '', '', ''];
        $rows[] = ['Nivel Operativo (NOPERA)', $val($nivelOperativo,'total_aprobado'), $val($nivelOperativo,'total_efectivo'), '', '', ''];

        return $rows;
    }
}
