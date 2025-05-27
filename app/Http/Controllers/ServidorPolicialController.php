<?php

// app/Http/Controllers/ServidorPolicialController.php
namespace App\Http\Controllers;

use App\Models\ServidorPolicial;
use Illuminate\Http\Request;

class ServidorPolicialController extends Controller
{
    public function index(Request $request)
    {
        // Obtén el filtro 'hijos_menor_igual_18' si está presente en la solicitud
        $hijos = $request->input('hijos_menor_igual_18');

        // Aplica el filtro en la consulta si 'hijos_menor_igual_18' está presente
        $servidores = ServidorPolicial::when($hijos, function($query) use ($hijos) {
            return $query->where('hijos_menor_igual_18', $hijos);
        })->get(); // Trae los registros filtrados

        // Pasa los servidores a la vista
        return view('servidores.index', compact('servidores'));
    }
}
