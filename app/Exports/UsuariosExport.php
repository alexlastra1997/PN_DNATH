<?php

namespace App\Exports;

use App\Models\Usuario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;


class UsuariosExport implements FromCollection
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Usuario::query();

        if ($this->request->filled('search')) {
            $query->where('cedula', 'like', '%' . $this->request->search . '%');
        }

        if ($this->request->filled('sexo')) {
            $query->whereIn('sexo', $this->request->sexo);
        }

        if ($this->request->filled('hijos18')) {
            $query->whereIn('hijos18', $this->request->hijos);
        }

        return $query->limit(5000)->get();

    }
}

