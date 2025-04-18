@extends('layouts.app') {{-- o tu layout principal --}}

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow mt-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-700">Perfil del Servidor Policial</h1>

    <table class="table-auto w-full text-sm text-left text-gray-600">
        <tbody>
            <tr><th class="font-medium">Cédula:</th><td>{{ $usuario->cedula }}</td></tr>
            <tr><th class="font-medium">Nombre:</th><td>{{ $usuario->apellidos_nombres }}</td></tr>
            <tr><th class="font-medium">Sexo:</th><td>{{ $usuario->sexo }}</td></tr>
            <tr><th class="font-medium">Número de Hijos:</th><td>{{ $usuario->hijos18 }}</td></tr>
            <tr><th class="font-medium">Cuadro Policial:</th><td>{{ $usuario->cuadro_policial }}</td></tr>
            {{-- Agrega más campos aquí según tu modelo --}}
        </tbody>
    </table>

    <div class="mt-6">
        <a href="{{ route('usuarios.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-black px-4 py-2 rounded">
           Volver
        </a>
    </div>
</div>
@endsection
