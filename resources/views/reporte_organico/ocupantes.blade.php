@extends('layouts.app')

@section('content')
<div class="p-4">
    <h2 class="text-lg font-semibold mb-4">Ocupantes de {{ $nomenclatura }} - {{ $cargo }}</h2>

    <table class="table-auto w-full border">
        <thead>
            <tr class="bg-gray-200">
                <th class="px-4 py-2">Cédula</th>
                <th class="px-4 py-2">Grado</th>
                <th class="px-4 py-2">Apellidos y Nombres</th>
                <th class="px-4 py-2">Estado de Traslado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ocupantes as $ocupante)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $ocupante->cedula }}</td>
                <td class="px-4 py-2">{{ $ocupante->grado }}</td>
                <td class="px-4 py-2">{{ $ocupante->apellidos_nombres }}</td>
                <td class="px-4 py-2">{{ $ocupante->estado_efectivo }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="text-center py-2 text-gray-500">No hay ocupantes.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
