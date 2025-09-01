@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-xl font-bold mb-4">Visualizador Orgánico</h1>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('reporte_organico.visualizador') }}" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        <input type="text" name="servicio" value="{{ request('servicio') }}" placeholder="Buscar Servicio" class="px-3 py-2 border rounded">
        <input type="text" name="nomenclatura" value="{{ request('nomenclatura') }}" placeholder="Buscar Nomenclatura" class="px-3 py-2 border rounded">
        <input type="text" name="cargo" value="{{ request('cargo') }}" placeholder="Buscar Cargo" class="px-3 py-2 border rounded">

        {{-- Selector de Estado --}}
        <select name="estado" class="px-3 py-2 border rounded">
            <option value="">-- Estado --</option>
            <option value="COMPLETO" {{ request('estado') == 'COMPLETO' ? 'selected' : '' }}>COMPLETO</option>
            <option value="EXCEDIDO" {{ request('estado') == 'EXCEDIDO' ? 'selected' : '' }}>EXCEDIDO</option>
            <option value="VACANTE" {{ request('estado') == 'VACANTE' ? 'selected' : '' }}>VACANTE</option>
        </select>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Buscar
        </button>
    </form>

   <form action="{{ route('reporte_organico.exportar') }}" method="GET">
        <input type="hidden" name="servicio" value="{{ request('servicio') }}">
        <input type="hidden" name="nomenclatura" value="{{ request('nomenclatura') }}">
        <input type="hidden" name="cargo" value="{{ request('cargo') }}">
        <input type="hidden" name="estado" value="{{ request('estado') }}">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Descargar Excel
        </button>
    </form>

    {{-- Tabla --}}
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border text-sm">
            <thead class="bg-gray-100 text-xs font-semibold text-gray-700 uppercase">
                <tr>
                    <th class="px-4 py-2">Servicio</th>
                    <th class="px-4 py-2">Nomenclatura</th>
                    <th class="px-4 py-2">Cargo</th>
                    <th class="px-4 py-2">Orgánico Aprobado</th>
                    <th class="px-4 py-2">Orgánico Efectivo</th>
                    <th class="px-4 py-2">Ver Ocupantes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($datos as $item)
                    @php
                        $efectivo = $item->organico_efectivo;
                        $aprobado = $item->organico_aprobado;
                        $estado = '';

                        if ($efectivo == $aprobado) $estado = 'COMPLETO';
                        elseif ($efectivo > $aprobado) $estado = 'EXCEDIDO';
                        else $estado = 'VACANTE';

                        $color = match(true) {
                            $estado === 'COMPLETO' => 'bg-green-500',
                            $estado === 'EXCEDIDO' => 'bg-red-500',
                            $estado === 'VACANTE' => 'bg-yellow-400',
                            default => 'bg-gray-400'
                        };
                    @endphp
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $item->servicio_organico }}</td>
                        <td class="px-4 py-2">{{ $item->nomenclatura_organico }}</td>
                        <td class="px-4 py-2">{{ $item->cargo_organico }}</td>
                        <td class="px-4 py-2">{{ $aprobado }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold text-white rounded-full {{ $color }}">
                                {{ $efectivo }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <form action="{{ route('reporte_organico.ocupantes') }}" method="GET">
                                <input type="hidden" name="nomenclatura" value="{{ $item->nomenclatura_organico }}">
                                <input type="hidden" name="cargo" value="{{ $item->cargo_organico }}">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1 rounded">
                                    Ver Ocupantes
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">No se encontraron resultados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $datos->appends(request()->query())->links() }}
    </div>
</div>


@endsection


