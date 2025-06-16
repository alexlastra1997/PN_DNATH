@extends('layouts.app')

@section('content')
<div class="space-y-8 px-4">

    

    <!-- FILTROS -->
    <form method="GET" action="{{ route('buscar.vacante.filtrar') }}" class="bg-white p-4 rounded shadow space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Selectores -->
            <div>
                <label class="block mb-1">Grado:</label>
                <select name="grado" class="w-full border p-2 rounded">
                    <option value="">-- Seleccione --</option>
                    @foreach ($grados as $grado)
                        <option value="{{ $grado }}" {{ request('grado') == $grado ? 'selected' : '' }}>{{ $grado }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1">Subsistema:</label>
                <select name="subsistema" class="w-full border p-2 rounded">
                    <option value="">-- Seleccione --</option>
                    @foreach ($subsistemas as $s)
                        <option value="{{ $s }}" {{ request('subsistema') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1">Tipo de Personal:</label>
                <select name="tipo_personal" class="w-full border p-2 rounded">
                    <option value="">-- Seleccione --</option>
                    @foreach ($tipos as $t)
                        <option value="{{ $t }}" {{ request('tipo_personal') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tiempo en cargo -->
            <div>
                <label class="block mb-1">Tiempo en el cargo:</label>
                <select name="tiempo" class="w-full border p-2 rounded">
                    <option value="">-- Seleccione --</option>
                    @foreach ([1, 2, 3, 4, 5] as $a)
                        <option value="{{ $a }}" {{ request('tiempo') == $a ? 'selected' : '' }}>
                            {{ $a == 5 ? 'Más de 4 años' : $a . ' año' . ($a > 1 ? 's' : '') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Búsqueda -->
            <div class="md:col-span-2 lg:col-span-3">
                <label class="block mb-1">Buscar por capacitación o títulos:</label>
                <input type="text" name="buscar" class="w-full border p-2 rounded" placeholder="Ej. Criminalística, Seguridad..." value="{{ request('buscar') }}">
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Buscar Vacante</button>
        </div>
    </form>

    <!-- CARDS SUPERIORES -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="#tabla_ocupadas" class="block p-6 bg-white rounded-xl shadow hover:bg-blue-50 transition">
            <h2 class="text-lg font-bold text-gray-700">Vacantes Ocupadas</h2>
            <p class="text-4xl font-bold text-blue-600 mt-2">{{ $usuarios->total() }}</p>
        </a>

        <a href="#tabla_vacantes" class="block p-6 bg-white rounded-xl shadow hover:bg-green-50 transition">
            <h2 class="text-lg font-bold text-gray-700">Vacantes Libres</h2>
            <p class="text-4xl font-bold text-green-600 mt-2">{{ $vacantes->total() }}</p>
        </a>
    </div>

    <!-- TABLA OCUPADAS -->
    <div id="tabla_ocupadas">
        <h2 class="text-xl font-bold mt-10 mb-4">Vacantes Ocupadas</h2>
        @if($usuarios->count())
            <table class="w-full table-auto border text-xs">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2">Grado</th>
                        <th class="border p-2">Nombre</th>
                        <th class="border p-2">Nomenclatura</th>
                        <th class="border p-2">Unidad (Org.)</th>
                        <th class="border p-2">Función (Org.)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="border p-2">{{ $u->grado }}</td>
                            <td class="border p-2">{{ $u->apellidos_nombres }}</td>
                            <td class="border p-2">{{ $u->nomenclatura_territorio_efectivo }}</td>
                            <td class="border p-2">{{ $u->unidad_organico ?? 'N/D' }}</td>
                            <td class="border p-2">{{ $u->funcion_organico ?? 'N/D' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $usuarios->withQueryString()->links() }}
            </div>
        @else
            <p class="text-gray-500">No hay servidores ocupando vacantes bajo los criterios seleccionados.</p>
        @endif
    </div>

    <!-- TABLA VACANTES LIBRES -->
    <div id="tabla_vacantes">
        <h2 class="text-xl font-bold mt-10 mb-4">Vacantes No Ocupadas</h2>
        @if($vacantes->count())
            <table class="w-full table-auto border text-xs">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2">Unidad</th>
                        <th class="border p-2">Nomenclatura</th>
                        <th class="border p-2">Grado</th>
                        <th class="border p-2">Función</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vacantes as $v)
                        <tr class="hover:bg-gray-50">
                            <td class="border p-2">{{ $v->unidad }}</td>
                            <td class="border p-2">{{ $v->nomenclatura }}</td>
                            <td class="border p-2">{{ $v->grado }}</td>
                            <td class="border p-2">{{ $v->funcion }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $vacantes->withQueryString()->links() }}
            </div>
        @else
            <p class="text-gray-500">No hay vacantes disponibles bajo los filtros aplicados.</p>
        @endif
    </div>

</div>
@endsection
