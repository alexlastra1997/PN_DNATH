<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class DistritoSheet implements FromView
{
    public function __construct(private array $data, private string $distrito) {}

    public function view(): View
    {
        $filas = $this->data['estadoPorDistrito'][$this->distrito] ?? [];
        $lista = $this->data['usuariosPorDistrito'][$this->distrito] ?? collect();

        return view('exports.ndesc_distrito', [
            'zona'           => $this->data['zona'],
            'subzonaNombre'  => $this->data['subzonaNombre'],
            'distrito'       => $this->distrito,
            'filas'          => $filas,
            'lista'          => $lista,
            'requisitosGrado'=> $this->data['requisitosGrado'],
        ]);
    }
}
