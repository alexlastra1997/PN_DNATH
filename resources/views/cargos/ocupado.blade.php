@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-xl font-bold mb-4">Servidores policiales con función similar a: "{{ $cargo }}"</h2>

    @if($usuarios->isEmpty())
        <p class="text-red-600">No se encontraron usuarios con esa función.</p>
    @else
        <table class="min-w-full bg-white shadow rounded border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">Cédula</th>
                    <th class="p-2 border">Apellidos y Nombres</th>
                    <th class="p-2 border">Grado</th>
                    <th class="p-2 border">Función</th>
                    <th class="p-2 border">Unidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                    <tr class="border-t">
                        <td class="p-2 border">{{ $usuario->cedula }}</td>
                        <td class="p-2 border">{{ $usuario->apellidos_nombres }}</td>
                        <td class="p-2 border">{{ $usuario->grado }}</td>
                        <td class="p-2 border">{{ $usuario->funcion }}</td>
                        <td class="p-2 border">{{ $usuario->unidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ url()->previous() }}" class="mt-4 inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
        ← Volver
    </a>
</div>
@endsection
