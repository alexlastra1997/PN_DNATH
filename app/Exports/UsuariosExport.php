<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsuariosExport implements FromCollection, WithHeadings
{
    protected $usuarios;

    public function __construct($usuarios)
    {
        $this->usuarios = $usuarios;
    }

    public function collection()
    {
        return $this->usuarios->map(function ($usuario) {
            return [
                'cedula' => $usuario->cedula,
                'nombre' => $usuario->apellidos_nombres,
                'sexo' => $usuario->sexo,
                'hijos_menor_igual_18' => $usuario->hijos_menor_igual_18,
            ];
        });
    }

    public function headings(): array
    {
        return ['Cédula', 'Nombre', 'Sexo', 'Hijos < 18'];
    }
}
