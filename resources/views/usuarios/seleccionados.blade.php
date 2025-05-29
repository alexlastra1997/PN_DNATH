@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-xl font-bold mb-4">Usuarios Seleccionados</h1>

    @if($usuarios->isEmpty())
        <p class="text-gray-600">No hay usuarios seleccionados.</p>
    @else

        <form method="GET" action="{{ route('usuarios.factibilidad') }}" class="mb-4 p-4 bg-gray-100 rounded space-y-4">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">

        <!-- Campos de lista dinámica -->
        @foreach(['estado_civil', 'provincia_trabaja', 'provincia_vive'] as $campo)
            <select name="{{ $campo }}" class="p-2 border rounded">
                <option value="">{{ ucwords(str_replace('_', ' ', $campo)) }}</option>
                @foreach($filtros[$campo] as $valor)
                    <option value="{{ $valor }}">{{ $valor }}</option>
                @endforeach
            </select>
        @endforeach

        <!-- Campos tipo Sí / No -->
        @foreach([
            'contrato_estudios', 'conyuge_policia_activo', 'enf_catast_sp', 'enf_catast_conyuge_hijos',
            'discapacidad_sp', 'discapacidad_conyuge_hijos', 'hijos_menor_igual_18', 'alertas',
            'novedad_situacion', 'maternidad'
        ] as $campo)
            <select name="{{ $campo }}" class="p-2 border rounded">
                <option value="">{{ ucwords(str_replace('_', ' ', $campo)) }}</option>
                <option value="si">Sí</option>
                <option value="no">No</option>
            </select>
        @endforeach

    </div>
    <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Analiza</button>
</form>


        <table class="min-w-full border bg-white rounded shadow">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-2">Cédula</th>
                    <th class="p-2">Grado</th>
                    <th class="p-2">Nombres</th>
                    <th class="p-2">Unidad</th>
                     <th class="p-2">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                    <tr class="border-t">
                        <td class="p-2">{{ $usuario->cedula }}</td>
                        <td class="p-2">{{ $usuario->grado }}</td>
                        <td class="p-2">{{ $usuario->apellidos_nombres }}</td>
                        <td class="p-2">{{ $usuario->funcion }}</td>
                        <td class="p-2">
                            <form action="{{ route('usuarios.eliminarSeleccionado', $usuario->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este usuario de la selección?')">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="mt-4">
        <a href="{{ route('usuarios.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Volver</a>
    </div>
</div>
@endsection
