<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteOrganico extends Model
{
    use HasFactory;

    protected $table = 'reporte_organico';

    protected $fillable = [
        'servicio_organico',
        'nomenclatura_organico',
        'cargo_organico',
        'grado_organico',
        'personal_organico',
        'numero_organico_ideal',
        'subsistema',
    ];
}
