@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Usuarios aptos según filtro</h2>

    @if($aptos->count())
        <table class="min-w-full border text-xs">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-2 py-1">Cédula</th>
                    <th class="border px-2 py-1">Grado</th>
                    <th class="border px-2 py-1">Nombre</th>
                    <th class="border px-2 py-1">Unidad</th>
                    <th class="border px-2 py-1">Función</th>
                     <th class="border px-2 py-1">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($aptos as $usuario)
                    <tr>
                        <td class="border px-2 py-1">{{ $usuario->cedula }}</td>
                        <td class="border px-2 py-1">{{ $usuario->grado}}</td>
                        <td class="border px-2 py-1">{{ $usuario->apellidos_nombres }}</td>
                        <td class="border px-2 py-1">{{ $usuario->nomenclatura_territorio_efectivo }}</td>
                        <td class="border px-2 py-1">{{ $usuario->descFuncion_territorio_efectivo}}</td>
                        <td class="border px-2 py-1">{{ $usuario->estado_territorio_efectivo}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-red-500">No se encontraron usuarios en esa provincia.</p>
    @endif

    <a href="{{ route('opciones') }}" class="mt-4 inline-block bg-gray-500 text-white px-4 py-2 rounded">Volver a opciones</a>
</div>
@endsection
