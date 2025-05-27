<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServidorPolicial extends Model
{
    protected $table = 'servidor_policials';  // Asegúrate de que coincida con el nombre de la tabla en la base de datos
    
    protected $fillable = [
        'cedula', 'apellidos_nombres', 'hijos_menor_igual_18', // Agrega más columnas si es necesario
    ];
}