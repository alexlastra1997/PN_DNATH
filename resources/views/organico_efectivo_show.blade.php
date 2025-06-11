@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">

    <h1 class="text-2xl font-bold mb-4">Usuarios Seleccionados</h1>

    @if(count($usuariosSeleccionados) > 0)
        <table class="min-w-full bg-white border border-gray-300 rounded shadow mb-6">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-2 border">Cédula</th>
                    <th class="p-2 border">Grado</th>
                    <th class="p-2 border">Nombre</th>
                    <th class="p-2 border">Nomenclatura Efectivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuariosSeleccionados as $usuario)
                    <tr>
                        <td class="p-2 border">{{ $usuario['cedula'] }}</td>
                        <td class="p-2 border">{{ $usuario['grado'] }}</td>
                        <td class="p-2 border">{{ $usuario['apellidos_nombres'] }}</td>
                        <td class="p-2 border">{{ $usuario['nomenclatura_efectivo'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-gray-600 mb-6">No has agregado usuarios aún.</p>
    @endif

    <!-- Buscador por Vacante -->
    <div class="bg-gray-100 p-4 mb-6 rounded shadow">
        <h2 class="text-lg font-semibold mb-2">Buscar Vacante por Cargo y Tiempo en el Cargo</h2>

        <form action="{{ route('organico.efectivo.buscarVacante') }}" method="GET" class="flex items-center gap-4 flex-wrap">
            <!-- Selector de cargo -->
            <select name="cargo" required class="border p-2 rounded w-96">
                <option value="">Seleccione Cargo</option>
                @foreach($cargos as $c)
                    <option value="{{ $c->cargo }}" {{ request('cargo') == $c->cargo ? 'selected' : '' }}>
                        {{ $c->numero }} - {{ $c->cargo }}
                    </option>
                @endforeach
            </select>

            <!-- Selector de tiempo -->
            <select name="tiempo" class="border p-2 rounded">
                <option value="">Tiempo en el cargo</option>
                <option value="2" {{ request('tiempo') == '2' ? 'selected' : '' }}>2 años</option>
                <option value="3" {{ request('tiempo') == '3' ? 'selected' : '' }}>3 años</option>
                <option value="4" {{ request('tiempo') == '4' ? 'selected' : '' }}>4 años</option>
                <option value="5+" {{ request('tiempo') == '5+' ? 'selected' : '' }}>Más de 5 años</option>
            </select>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Buscar</button>
        </form>
        </div>

      


    <!-- Resultados de Búsqueda -->
    @if(isset($usuariosCoincidentes))
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-2 text-gray-700">
                Usuarios con cargo: <span class="text-blue-700">{{ $cargoSeleccionado }}</span>
                @if($tiempoSeleccionado)
                    y con al menos 
                    <span class="text-blue-700">
                        {{ $tiempoSeleccionado == '5+' ? '5 años' : $tiempoSeleccionado . ' años' }}
                    </span> en el cargo
                @endif
            </h2>

            @if(count($usuariosCoincidentes) > 0)
                <table class="min-w-full bg-white border border-gray-300 rounded shadow">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2 border">Cédula</th>
                            <th class="p-2 border">Grado</th>
                            <th class="p-2 border">Nombre</th>
                            <th class="p-2 border">Función</th>
                            <th class="p-2 border">Fecha en Cargo</th>
                            <th class="p-2 border">Años en Cargo</th>
                            <th class="p-2 border">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuariosCoincidentes as $usuario)
                            <tr>
                                <td class="p-2 border">{{ $usuario->cedula }}</td>
                                <td class="p-2 border">{{ $usuario->grado }}</td>
                                <td class="p-2 border">{{ $usuario->apellidos_nombres }}</td>
                                <td class="p-2 border">{{ $usuario->funcion }}</td>
                                <td class="p-2 border">
                                    {{ $usuario->fecha_territorio_efectivo ? \Carbon\Carbon::parse($usuario->fecha_territorio_efectivo)->format('Y-m-d') : '-' }}
                                </td>
                                <td class="p-2 border">
                                    @if($usuario->fecha_territorio_efectivo)
                                        {{ \Carbon\Carbon::parse($usuario->fecha_territorio_efectivo)->diffInYears(now()) }} años
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-2 border text-center">
                                    <form action="{{ route('organico.efectivo.evaluar') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="cedula_b" value="{{ $usuario->cedula }}">
                                        <button class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Evaluar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Paginación -->
                <div class="mt-4">
                    {{ $usuariosCoincidentes->links('pagination::tailwind') }}
                </div>
            @else
                <p class="text-gray-600">No se encontraron usuarios con ese cargo y tiempo.</p>
            @endif
        </div>
    @endif

    <!-- Botón de volver -->
    <div class="mt-6">
        <a href="{{ route('organico.efectivo') }}" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">
            Volver a listado
        </a>
    </div>

</div>
@endsection
