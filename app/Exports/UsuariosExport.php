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
                'grado' => $usuario->grado,
                'nombre' => $usuario->apellidos_nombres,
                'titulos_senescyt' => $usuario->titulos_senescyt,
                'Capacitaciones' => $usuario->capacitacion,
                'Unidad' => $usuario->unidad,
                'Funcion' => $usuario->funcion,
            ];
        });
    }

    public function headings(): array
    {
        return ['Cédula', 'grado', 'Nombre','titulos_senescyt','capacitacion','unidad','funcion'];
    }
}
