@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Orgánico Efectivo</h1>

    <form method="GET" action="{{ route('organico.efectivo') }}" class="mb-4 flex flex-wrap items-center gap-4">
    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por cédula o nombre"
           class="border p-2 rounded w-64">

    <select name="grado" class="border p-2 rounded">
        <option value="">-- Filtrar por Grado --</option>
        @foreach ($grados as $grado)
            <option value="{{ $grado }}" {{ request('grado') == $grado ? 'selected' : '' }}>{{ $grado }}</option>
        @endforeach
    </select>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filtrar</button>

    <a href="{{ route('organico.efectivo') }}" class="text-sm text-gray-600 underline ml-2">Limpiar filtros</a>
</form>


    <div class="mb-4 flex justify-between">
        <a href="{{ route('organico.efectivo.seleccionados') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Ver Seleccionados
        </a>
        <form method="POST" action="{{ route('organico.efectivo.limpiar') }}">
            @csrf
            <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Vaciar Lista</button>
        </form>
    </div>

    <table class="min-w-full bg-white border border-gray-300 rounded shadow">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border">Cédula</th>
                <th class="p-2 border">Grado</th>
                <th class="p-2 border">Nombre</th>
                <th class="p-2 border">Nomenclatura Efectivo</th>
                <th class="p-2 border">Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr class="hover:bg-gray-100">
                    <td class="p-2 border">{{ $usuario->cedula }}</td>
                    <td class="p-2 border">{{ $usuario->grado }}</td>
                    <td class="p-2 border">{{ $usuario->apellidos_nombres }}</td>
                    <td class="p-2 border">{{ $usuario->nomenclatura_territorio_efectivo }}</td>
                    <td class="p-2 border text-center">
                        <form action="{{ route('organico.efectivo.agregar') }}" method="POST">
                            @csrf
                            <input type="hidden" name="cedula" value="{{ $usuario->cedula }}">
                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">+ Agregar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">
        {{ $usuarios->links('pagination::tailwind') }}
    </div>

</div>
@endsection
