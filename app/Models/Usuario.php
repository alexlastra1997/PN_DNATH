<?php

// app/Models/Usuario.php

namespace App\Models;
use App\Models\Cargo;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    // Asegúrate de que el nombre de la tabla sea 'usuarios'
    protected $table = 'usuarios';

    // Define los campos que quieres permitir asignar masivamente
    protected $fillable = ['cedula', 'grado', 'apellidos_nombres','titulo_senecyt','nomenclatura_territorio_efectivo'];

   public function obtenerFuncionDesdeNomenclatura()
{
    $nomenclaturaUsuario = $this->nomenclatura_territorio_efectivo;

    // Si no hay nomenclatura, retorno null
    if (!$nomenclaturaUsuario) {
        return null;
    }

    // Buscamos coincidencias donde el nombre esté contenido
    $coincidencias = \App\Models\ReporteOrganico::where('nomenclatura', 'LIKE', "%$nomenclaturaUsuario%")->get();

    // Si no hay coincidencias, intentar al revés
    if ($coincidencias->isEmpty()) {
        $coincidencias = \App\Models\ReporteOrganico::where('nomenclatura', 'LIKE', "%$nomenclaturaUsuario%")->orWhere('nomenclatura', 'LIKE', "%$nomenclaturaUsuario%")->get();
    }

    // Si hay coincidencias, buscamos la más larga
    if (!$coincidencias->isEmpty()) {
        $coincidenciaMasLarga = $coincidencias->sortByDesc(function ($item) {
            return strlen($item->nomenclatura);
        })->first();

        return $coincidenciaMasLarga->funcion ?? null;
    }

    // Si no encontramos nada
    return null;
}


   
}
