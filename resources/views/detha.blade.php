
@extends('layouts.app')
@section('content')
    <h1 class="text-2xl font-bold mb-4">DETHA</h1>

    <table class="min-w-full bg-white border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2">Cédula</th>
                <th class="border p-2">Grado</th>
                <th class="border p-2">Nombre</th>
                <th class="border p-2">Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
            <tr>
                <td class="border p-2">{{ $usuario->cedula }}</td>
                <td class="border p-2">{{ $usuario->grado }}</td>
                <td class="border p-2">{{ $usuario->apellidos_nombres }}</td>
                <td class="border p-2">
                    <a href="{{ route('detha.show', $usuario->id) }}" class="bg-blue-500 text-white px-2 py-1 rounded">Ver Información</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $usuarios->links() }}
    </div>

@endsection