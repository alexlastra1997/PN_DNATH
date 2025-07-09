@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Usuarios filtrados por provincia</h2>

    @if($usuariosFiltrados->count())
        <table class="min-w-full border text-xs">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-2 py-1">Cédula</th>
                    <th class="border px-2 py-1">Nombre</th>
                    <th class="border px-2 py-1">Correo</th>
                    <th class="border px-2 py-1">Provincia Trabaja</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuariosFiltrados as $usuario)
                    <tr>
                        <td class="border px-2 py-1">{{ $usuario->cedula }}</td>
                        <td class="border px-2 py-1">{{ $usuario->nombres }}</td>
                        <td class="border px-2 py-1">{{ $usuario->email }}</td>
                        <td class="border px-2 py-1">{{ $usuario->provincia_trabaja }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-red-500">No se encontraron usuarios en esa provincia.</p>
    @endif

    <a href="{{ route('opciones') }}" class="mt-4 inline-block bg-gray-500 text-white px-4 py-2 rounded">Regresar</a>
</div>
@endsection
