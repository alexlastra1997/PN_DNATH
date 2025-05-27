<?php

// app/Models/Usuario.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    // Asegúrate de que el nombre de la tabla sea 'usuarios'
    protected $table = 'usuarios';

    // Define los campos que quieres permitir asignar masivamente
    protected $fillable = ['cedula', 'apellidos_nombres', 'hijos_menor_igual_18'];
}
