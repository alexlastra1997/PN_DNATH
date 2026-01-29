@extends('layouts.app')

@section('content')
    @php
        $selServicios     = (array) request()->input('servicio', []);
        $selNomenclaturas = (array) request()->input('nomenclatura', []);
        $selCargos        = (array) request()->input('cargo', []);
        $selEstados       = (array) request()->input('estado', []);
        $selSubsistemas   = (array) request()->input('subsistema', []);
        $selGrados        = (array) request()->input('grado_organico', []);

        $estadoOpts = ['COMPLETO','VACANTE','EXCEDIDO'];

        $totalAprobado = (int) ($totales->total_aprobado ?? 0);
        $totalEfectivo = (int) ($totales->total_efectivo ?? 0);

        $sumVac = (int) ($statsSubsistema->sum('cargos_vacantes')  ?? 0);
        $sumCom = (int) ($statsSubsistema->sum('cargos_completos') ?? 0);
        $sumExc = (int) ($statsSubsistema->sum('cargos_excedidos') ?? 0);
    @endphp

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 text-gray-900 dark:text-gray-100">
        <h1 class="text-xl md:text-2xl font-semibold tracking-tight mb-4 text-gray-800 dark:text-gray-200">
            Reporte orgánico — Visualizador
        </h1>

        <form method="GET" action="{{ route(request()->route()->getName()) }}" class="space-y-3 mb-4" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-7 gap-2">
                <x-multi-check id="ms-servicio"   name="servicio[]"       placeholder="Servicio (múltiple)"         searchPlaceholder="Buscar servicio…"       :options="$opcionesServicio"     :selected="$selServicios" />
                <x-multi-check id="ms-nom"        name="nomenclatura[]"   placeholder="Nomenclatura (múltiple)"     searchPlaceholder="Buscar nomenclatura…"   :options="$opcionesNomenclatura" :selected="$selNomenclaturas" />
                <x-multi-check id="ms-cargo"      name="cargo[]"          placeholder="Cargo (múltiple)"            searchPlaceholder="Buscar cargo…"          :options="$opcionesCargo"        :selected="$selCargos" />
                <x-multi-check id="ms-estado"     name="estado[]"         placeholder="Estado (múltiple)"           searchPlaceholder="Buscar estado…"         :options="$estadoOpts"           :selected="$selEstados" />
                <x-multi-check id="ms-subsistema" name="subsistema[]"     placeholder="Subsistema (múltiple)"       searchPlaceholder="Buscar subsistema…"     :options="$opcionesSubsistema"   :selected="$selSubsistemas" />
                <x-multi-check id="ms-grado"      name="grado_organico[]" placeholder="Grado (múltiple)"            searchPlaceholder="Buscar grado…"          :options="$opcionesGradoOrganico" :selected="$selGrados" />
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-700 rounded-lg
                       hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300
                       dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Filtrar
                </button>

                <a href="{{ route(request()->route()->getName()) }}"
                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-900 bg-gray-100 rounded-lg
                      hover:bg-gray-200 focus:outline-none focus:ring-4 focus:ring-gray-300
                      dark:text-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-800">
                    Limpiar
                </a>

                <a
                    href="{{ route('reporte_organico.exportar_excel', request()->query()) }}"
                    class="inline-flex items-center px-3 py-2 text-xs font-medium text-white bg-emerald-600 rounded hover:bg-emerald-700">
                    Descargar Excel
                </a>

                <button type="button" data-modal-target="modalSubsistema" data-modal-toggle="modalSubsistema"
                        class="ml-auto inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-violet-700 rounded-lg
                       hover:bg-violet-800 focus:outline-none focus:ring-4 focus:ring-violet-300
                       dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800">
                    Resumen por subsistema
                </button>
            </div>
        </form>

        {{-- Totales (compactos) --}}
        <div class="grid grid-cols-2 gap-2 mb-3">
            <div class="p-3 rounded-lg border border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700">
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Orgánico aprobado (suma)</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($totalAprobado,0,',','.') }}</p>
            </div>
            <div class="p-3 rounded-lg border border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700">
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Orgánico efectivo (suma)</p>
                <p class="mt-1 text-2xl font-semibold">{{ number_format($totalEfectivo,0,',','.') }}</p>
            </div>
        </div>

        {{-- Leyenda compacta --}}
        <div class="flex flex-wrap items-center gap-3 text-[11px] mb-2 text-gray-700 dark:text-gray-300">
            <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-green-500"></span> COMPLETO</span>
            <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-yellow-400"></span> VACANTE</span>
            <span class="inline-flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-500"></span> EXCEDIDO</span>
        </div>

        {{-- Tabla compacta --}}
        <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full table-fixed text-xs leading-tight text-left rtl:text-right text-gray-700 dark:text-gray-300">
                <thead class="text-[11px] uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
                <tr>
                    <th class="px-3 py-2 w-48">Servicio</th>
                    <th class="px-3 py-2 w-64">Nomenclatura</th>
                    <th class="px-3 py-2 w-72">Cargo</th>
                    <th class="px-3 py-2 w-28 text-center">Aprobado</th>
                    <th class="px-3 py-2 w-28 text-center">Efectivo</th>
                    <th class="px-3 py-2 w-20 text-center">Alertas</th>
                    <th class="px-3 py-2 w-32 text-center">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse($datos as $fila)
                    @php
                        $ap = (int) $fila->organico_aprobado;
                        $ef = (int) $fila->organico_efectivo;
                        $dif = $ef - $ap;
                        $estado = $dif === 0 ? 'ok' : ($dif < 0 ? 'vac' : 'exc');
                    @endphp
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-3 py-2 whitespace-nowrap overflow-hidden text-ellipsis">{{ $fila->servicio_organico }}</td>
                        <td class="px-3 py-2 whitespace-nowrap overflow-hidden text-ellipsis">{{ $fila->nomenclatura_organico }}</td>

                        <td class="px-3 py-2">
                            <div class="whitespace-nowrap overflow-x-auto no-scrollbar">{{ $fila->cargo_organico }}</div>
                        </td>

                        <td class="px-3 py-2 text-center font-semibold">{{ $ap }}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-flex items-center gap-1.5">
                                <span>{{ $ef }}</span>
                                <span class="inline-block w-2 h-2 rounded-full
                                    @if($estado==='ok') bg-green-500 @elseif($estado==='vac') bg-yellow-400 @else bg-red-500 @endif"></span>
                            </span>
                        </td>

                        <td class="px-3 py-2 text-center">
                            @if(!empty($fila->tiene_alerta))
                                <span class="inline-flex items-center px-2 py-1 rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
                                    Sí
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="px-3 py-2 text-center">
                            <a
                                href="{{ route('reporte_organico.ocupantes', ['nomenclatura' => $fila->nomenclatura_organico, 'cargo' => $fila->cargo_organico]) }}"
                                class="inline-flex items-center px-2.5 py-1.5 text-[11px] font-medium text-white bg-slate-700 rounded hover:bg-slate-800">
                                Ver ocupante
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">
                            No hay resultados con los filtros seleccionados.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-3">
            {{ $datos->links() }}
        </div>

        {{-- Modal Resumen por subsistema --}}
        <div id="modalSubsistema" tabindex="-1" aria-hidden="true"
             class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Resumen por subsistema</h3>
                        <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-700 dark:hover:text-white"
                                data-modal-hide="modalSubsistema">
                            ✕
                        </button>
                    </div>

                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-3 gap-2 text-[11px]">
                            <div class="p-2 rounded border dark:border-gray-700">
                                <div class="text-gray-500 dark:text-gray-400">Vacantes</div>
                                <div class="text-lg font-semibold">{{ $sumVac }}</div>
                            </div>
                            <div class="p-2 rounded border dark:border-gray-700">
                                <div class="text-gray-500 dark:text-gray-400">Completos</div>
                                <div class="text-lg font-semibold">{{ $sumCom }}</div>
                            </div>
                            <div class="p-2 rounded border dark:border-gray-700">
                                <div class="text-gray-500 dark:text-gray-400">Excedidos</div>
                                <div class="text-lg font-semibold">{{ $sumExc }}</div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="text-[11px] uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="px-3 py-2">Subsistema</th>
                                    <th class="px-3 py-2 text-center">Vacantes</th>
                                    <th class="px-3 py-2 text-center">Completos</th>
                                    <th class="px-3 py-2 text-center">Excedidos</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($statsSubsistema as $s)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-3 py-2">{{ $s->subsistema }}</td>
                                        <td class="px-3 py-2 text-center">{{ (int)$s->cargos_vacantes }}</td>
                                        <td class="px-3 py-2 text-center">{{ (int)$s->cargos_completos }}</td>
                                        <td class="px-3 py-2 text-center">{{ (int)$s->cargos_excedidos }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex items-center p-4 border-t border-gray-200 rounded-b dark:border-gray-700">
                        <button data-modal-hide="modalSubsistema" type="button"
                                class="ms-auto text-xs px-3 py-1.5 rounded bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
