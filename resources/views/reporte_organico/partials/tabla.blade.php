{{-- resources/views/reporte_organico/partials/tabla.blade.php --}}
<div class="overflow-x-auto">
    <table class="min-w-full text-sm text-gray-800 dark:text-gray-100">
        <thead class="bg-gray-100 dark:bg-gray-700/60">
        <tr>
            <th class="px-3 py-2 text-left">Servicio</th>
            <th class="px-3 py-2 text-left">Nomenclatura</th>
            <th class="px-3 py-2 text-left">Cargo</th>
            <th class="px-3 py-2 text-right">Aprobado</th>
            <th class="px-3 py-2 text-right">Efectivo</th>
            <th class="px-3 py-2 text-left">Estado</th>
            <th class="px-3 py-2 text-left">Acciones</th>
        </tr>
        </thead>
        <tbody>
        @forelse($datos as $r)
            @php
                $ap = (int) $r->organico_aprobado;
                $ef = (int) $r->organico_efectivo;
                $estado = $ef < $ap ? 'VACANTE' : ($ef == $ap ? 'COMPLETO' : 'EXCEDIDO');
            @endphp
            <tr class="border-b border-gray-100 dark:border-gray-700/50">
                <td class="px-3 py-2 align-top">{{ $r->servicio_organico }}</td>
                <td class="px-3 py-2 align-top">{{ $r->nomenclatura_organico }}</td>
                <td class="px-3 py-2 align-top">{{ $r->cargo_organico }}</td>
                <td class="px-3 py-2 text-right align-top">{{ number_format($ap) }}</td>
                <td class="px-3 py-2 text-right align-top">{{ number_format($ef) }}</td>
                <td class="px-3 py-2 align-top">
                    @if($estado === 'EXCEDIDO')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-800/30 dark:text-red-300">EXCEDIDO</span>
                    @elseif($estado === 'VACANTE')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-800/30 dark:text-amber-300">VACANTE</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-800/30 dark:text-emerald-300">COMPLETO</span>
                    @endif
                </td>
                <td class="px-3 py-2 align-top">
                    <a
                        href="{{ url('reporte_organico/ocupantes') }}?nomenclatura={{ urlencode($r->nomenclatura_organico) }}&cargo={{ urlencode($r->cargo_organico) }}"
                        class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white">
                        Ver ocupantes
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-3 py-6 text-center text-sm text-gray-500">
                    Sin resultados para los filtros aplicados.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
