<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ComandantesSheet implements FromView
{
    public function __construct(private array $data) {}

    public function view(): View
    {
        return view('exports.ndesc_comandantes', [
            'zona'           => $this->data['zona'],
            'subzonaNombre'  => $this->data['subzonaNombre'],
            'leadersSubzona' => $this->data['leadersSubzona'],
            'requisitosGrado'=> $this->data['requisitosGrado'],
        ]);
    }
}
