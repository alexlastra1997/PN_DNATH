<table class="min-w-full text-xs border">
    <thead>
        <tr class="bg-gray-200">
            <th class="px-4 py-2">Servicio</th>
            <th class="px-4 py-2">Nomenclatura</th>
            <th class="px-4 py-2">Cargo</th>
            <th class="px-4 py-2">Orgánico Aprobado</th>
            <th class="px-4 py-2">Ver Ocupantes</th>
        </tr>
    </thead>
    <tbody>
        @if ($datos->isEmpty())
            <tr><td colspan="5" class="text-center py-4 text-gray-500">No se encontraron resultados.</td></tr>
        @else
            @foreach($datos as $item)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $item->servicio_organico }}</td>
                <td class="px-4 py-2">{{ $item->nomenclatura_organico }}</td>
                <td class="px-4 py-2">{{ $item->cargo_organico }}</td>
                <td class="px-4 py-2">{{ $item->organico_aprobado ?? 0 }}</td>
                <td class="px-4 py-2">
                    <form action="{{ route('ver-ocupantes') }}" method="GET">
                        <input type="hidden" name="nomenclatura" value="{{ $item->nomenclatura_organico }}">
                        <input type="hidden" name="cargo" value="{{ $item->cargo_organico }}">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1 rounded">
                            Ver Ocupantes
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>

@if (method_exists($datos, 'links'))
<div class="mt-4">
    {{ $datos->links() }}
</div>
@endif