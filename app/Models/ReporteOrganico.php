<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteOrganico extends Model
{
    use HasFactory;

    protected $table = 'reporte_organico';

    protected $fillable = [
        'unidad',
        'nomenclatura',
        'grado',
        'funcion',
        'tipo_personal',
        'cantidad_ideal',
        'cantidad_real',
        'diferencia',
    ];
}
