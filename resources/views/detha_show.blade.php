@extends('layouts.app')
@section('content')
    <h1 class="text-xl font-bold mb-4">Información del Usuario</h1>

    <div class="mb-4">
        <a href="{{ route('detha.edit', $usuario->id) }}" class="bg-green-500 text-white px-3 py-2 rounded">Actualizar Información</a>
    </div>

    <table class="min-w-full bg-white border">
        @foreach($usuario->toArray() as $campo => $valor)
            <tr>
                <th class="border p-2 text-left capitalize">{{ $campo }}</th>
                <td class="border p-2">{{ $valor }}</td>
            </tr>
        @endforeach
    </table>

    <div class="mt-4">
        <a href="{{ route('detha.index') }}" class="bg-gray-500 text-white px-3 py-2 rounded">Volver</a>
    </div>
@endsection