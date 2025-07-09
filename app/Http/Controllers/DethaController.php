<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class DethaController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::select('id', 'cedula', 'grado', 'apellidos_nombres')->paginate(10);
        return view('detha', compact('usuarios'));
    }

    public function show($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('detha_show', compact('usuario'));
    }

    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        return view('detha_edit', compact('usuario'));
    }

    public function update(Request $request, $id)
{
    $usuario = Usuario::findOrFail($id);

    $usuario->direccion_unidad_zona_policia = $request->direccion_unidad_zona_policia;
    $usuario->sub_zona_policia = $request->sub_zona_policia;
    $usuario->distrito = $request->distrito;
    $usuario->circuito_departamento_seccion = $request->circuito_departamento_seccion;
    $usuario->subcircuito = $request->subcircuito;
    $usuario->funcion_asignada = $request->funcion_asignada;
    $usuario->fecha_presentacion_nueva = $request->fecha_presentacion_nueva;
    $usuario->novedad = $request->novedad;
    $usuario->dependencia_destino = $request->dependencia_destino;
    $usuario->detalle_novedad_nueva_unidad = $request->detalle_novedad_nueva_unidad;
    $usuario->fecha_novedad = $request->fecha_novedad;
    $usuario->tipo_documento = $request->tipo_documento;
    $usuario->documento_referencia = $request->documento_referencia;
    $usuario->numero_grupo_trabajo = $request->numero_grupo_trabajo;
    $usuario->grupo = $request->grupo;
    $usuario->modalidad = $request->modalidad;
    $usuario->tipo_sangre = $request->tipo_sangre;
    $usuario->licencia_conducir = $request->licencia_conducir;
    $usuario->correo_electronico = $request->correo_electronico;
    $usuario->numero_celular = $request->numero_celular;
    $usuario->numero_celular_familiar = $request->numero_celular_familiar;

    $usuario->save();

    return redirect()->route('detha.show', $usuario->id)->with('success', 'Información actualizada correctamente.');
}

}
