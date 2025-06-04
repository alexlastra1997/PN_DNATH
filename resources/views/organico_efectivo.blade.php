

@extends('layouts.app')

@section('importar')
    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-center">Organico Efectivo</h1>

        <form method="GET" class="flex flex-wrap gap-4 mb-6">

    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por cédula o nombre" class="border rounded p-2">

    <select name="cdg_promocion[]" multiple class="border rounded p-2 w-48 h-32">
        <option disabled>Seleccione Promoción</option>
        @foreach($cdgPromociones as $promocion)
            <option value="{{ $promocion }}" {{ in_array($promocion, (array) $filtroCdgPromocion) ? 'selected' : '' }}>
                {{ $promocion }}
            </option>
        @endforeach
    </select>

    <select name="provincia_vive" class="border rounded p-2 w-48">
        <option value="">Seleccione Provincia donde Vive</option>
        @foreach($provinciasVive as $provincia)
            <option value="{{ $provincia }}" {{ $filtroProvinciaVive == $provincia ? 'selected' : '' }}>
                {{ $provincia }}
            </option>
        @endforeach
    </select>

    <select name="provincia_trabaja" class="border rounded p-2 w-48">
        <option value="">Seleccione Provincia donde Trabaja</option>
        @foreach($provinciasTrabaja as $provincia)
            <option value="{{ $provincia }}" {{ $filtroProvinciaTrabaja == $provincia ? 'selected' : '' }}>
                {{ $provincia }}
            </option>
        @endforeach
    </select>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filtrar</button>
</form>



        <!-- Tabla SIEMPRE -->
        <div class="overflow-x-auto bg-white shadow-md rounded-lg mt-6">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Cédula</th>
                        <th class="py-3 px-6 text-left">Grado</th>
                        <th class="py-3 px-6 text-left">Nombre y Apellidos</th>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-left">Nomenclatura</th>
                        <th class="py-3 px-6 text-left">Telegrama</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse ($datos as $usuario)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6">{{ $usuario['cedula'] }}</td>
                            <td class="py-3 px-6">{{ $usuario['grado'] }}</td>
                            <td class="py-3 px-6">{{ $usuario['apellidos_nombres'] }}</td>
                            <td class="py-3 px-6">
                                <span class="{{ $usuario['origen'] === 'traslado' ? 'text-red-600 font-bold' : 'text-green-600 font-bold' }}">
                                    {{ $usuario['fecha'] ?? 'Sin fecha disponible' }}
                                </span>
                            </td>
                            <td class="py-3 px-6">{{ $usuario['nomenclatura'] ?? '-' }}</td>
                            <td class="py-3 px-6">{{ $usuario['telegrama'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-3 px-6 text-center">No hay registros para mostrar en este nivel.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
       
    </div>
 @endsection

