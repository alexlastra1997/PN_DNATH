<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class SubzonaSheet implements FromView
{
    public function __construct(private array $data) {}

    public function view(): View
    {
        $resumen = $this->data['estadoPorDistrito']['SIN DISTRITO'] ?? [];
        $lista   = $this->data['usuariosPorDistrito']['SIN DISTRITO'] ?? collect();

        return view('exports.ndesc_subzona', [
            'zona'           => $this->data['zona'],
            'subzonaNombre'  => $this->data['subzonaNombre'],
            'resumen'        => $resumen,
            'lista'          => $lista,
            'requisitosGrado'=> $this->data['requisitosGrado'],
            'distrito'       => 'SIN DISTRITO',
        ]);
    }
}
