<?php
namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class ReporteOrganicoExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('reporte_organico as ro')
            ->select(
                'ro.servicio_organico',
                'ro.nomenclatura_organico',
                'ro.cargo_organico',
                'ro.numero_organico_ideal as organico_aprobado',
                DB::raw('(SELECT COUNT(*) FROM usuarios u WHERE u.nomenclatura_efectiva = ro.nomenclatura_organico AND u.funcion_efectiva = ro.cargo_organico) as organico_efectivo')
            );

        // Aplicar filtros del request
        if ($this->request->filled('servicio')) {
            $query->where('ro.servicio_organico', 'like', '%' . $this->request->servicio . '%');
        }

        if ($this->request->filled('nomenclatura')) {
            $query->where('ro.nomenclatura_organico', 'like', '%' . $this->request->nomenclatura . '%');
        }

        if ($this->request->filled('cargo')) {
            $query->where('ro.cargo_organico', 'like', '%' . $this->request->cargo . '%');
        }

        if ($this->request->filled('estado')) {
            switch ($this->request->estado) {
                case 'VACANTE':
                    $query->havingRaw('organico_efectivo < organico_aprobado');
                    break;
                case 'COMPLETO':
                    $query->havingRaw('organico_efectivo = organico_aprobado');
                    break;
                case 'EXCEDIDO':
                    $query->havingRaw('organico_efectivo > organico_aprobado');
                    break;
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Servicio Orgánico',
            'Nomenclatura Orgánico',
            'Cargo Orgánico',
            'Orgánico Aprobado',
            'Orgánico Efectivo',
        ];
    }

    public function map($row): array
    {
        return [
            $row->servicio_organico,
            $row->nomenclatura_organico,
            $row->cargo_organico,
            $row->organico_aprobado,
            $row->organico_efectivo,
        ];
    }
}
