@extends('layouts.app')

@section('content')
    @php
        $selServicios     = (array) request()->input('servicio', []);
        $selNomenclaturas = (array) request()->input('nomenclatura', []);
        $selCargos        = (array) request()->input('cargo', []);
        $selEstados       = (array) request()->input('estado', []);
        $selSubsistemas   = (array) request()->input('subsistema', []);

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
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-2">
                <x-multi-check id="ms-servicio"   name="servicio[]"       placeholder="Servicio (múltiple)"         searchPlaceholder="Buscar servicio…"       :options="$opcionesServicio"     :selected="$selServicios" />
                <x-multi-check id="ms-nom"        name="nomenclatura[]"   placeholder="Nomenclatura (múltiple)"     searchPlaceholder="Buscar nomenclatura…"   :options="$opcionesNomenclatura" :selected="$selNomenclaturas" />
                <x-multi-check id="ms-cargo"      name="cargo[]"          placeholder="Cargo (múltiple)"            searchPlaceholder="Buscar cargo…"          :options="$opcionesCargo"        :selected="$selCargos" />
                <x-multi-check id="ms-estado"     name="estado[]"         placeholder="Estado (múltiple)"           searchPlaceholder="Buscar estado…"         :options="$estadoOpts"           :selected="$selEstados" />
                <x-multi-check id="ms-subsistema" name="subsistema[]"     placeholder="Subsistema (múltiple)"       searchPlaceholder="Buscar subsistema…"     :options="$opcionesSubsistema"   :selected="$selSubsistemas" />
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

        {{-- Tabla compacta (sin columna de Grado(s)) --}}
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

                        {{-- Cargo en una sola línea (scroll si es muy largo) --}}
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
                                <span class="inline-flex items-center text-[10px] font-medium px-1.5 py-0.5 rounded
                                             text-red-800 bg-red-100 dark:text-red-200 dark:bg-red-900/50">⚠️</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="px-3 py-2 text-center">
                            <a href="{{ route('reporte_organico.ocupantes', ['nomenclatura'=>$fila->nomenclatura_organico,'cargo'=>$fila->cargo_organico]) }}"
                               class="inline-flex items-center px-2.5 py-1 text-[11px] font-medium text-white bg-violet-700 rounded
                                      hover:bg-violet-800 focus:outline-none focus:ring-4 focus:ring-violet-300
                                      dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800">
                                Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td colspan="7" class="px-3 py-3 text-center text-gray-500 dark:text-gray-400">
                            No hay resultados con los filtros actuales.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-2">
            {{ $datos->withQueryString()->links() }}
        </div>
    </div>

    {{-- Modal resumen --}}
    <div id="modalSubsistema" tabindex="-1" aria-hidden="true"
         class="hidden fixed inset-0 z-50 overflow-y-auto overflow-x-hidden justify-center items-center">
        <div class="relative p-4 w-full max-w-5xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="flex items-center justify-between p-3 border-b rounded-t dark:border-gray-700">
                    <h3 class="text-sm md:text-base font-semibold text-gray-800 dark:text-gray-200">Resumen por subsistema</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg
                                     text-sm w-8 h-8 ms-auto inline-flex justify-center items-center
                                     dark:hover:bg-gray-700 dark:hover:text-white"
                            data-modal-hide="modalSubsistema">
                        <svg class="w-3 h-3" viewBox="0 0 14 14" fill="none"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 12 12M13 1 1 13"/></svg>
                        <span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="p-3 space-y-3">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                        <div class="lg:col-span-2 overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="w-full text-[11px] text-left rtl:text-right text-gray-700 dark:text-gray-300">
                                <thead class="text-[10px] uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="px-3 py-2">Subsistema</th>
                                    <th class="px-3 py-2 text-right">Aprobado</th>
                                    <th class="px-3 py-2 text-right">Efectivo</th>
                                    <th class="px-3 py-2 text-right">Vacantes</th>
                                    <th class="px-3 py-2 text-right">Completos</th>
                                    <th class="px-3 py-2 text-right">Excedidos</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($statsSubsistema as $row)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-3 py-2">{{ $row->subsistema }}</td>
                                        <td class="px-3 py-2 text-right">{{ (int)$row->total_aprobado }}</td>
                                        <td class="px-3 py-2 text-right">{{ (int)$row->total_efectivo }}</td>
                                        <td class="px-3 py-2 text-right">{{ (int)$row->cargos_vacantes }}</td>
                                        <td class="px-3 py-2 text-right">{{ (int)$row->cargos_completos }}</td>
                                        <td class="px-3 py-2 text-right">{{ (int)$row->cargos_excedidos }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 rounded-lg border border-gray-200 bg-white dark:bg-gray-900 dark:border-gray-700">
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-1.5">Distribución de cargos</p>
                            <canvas id="chartSubsistema" width="320" height="320"></canvas>
                            <div class="mt-2 text-[11px]">
                                <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-green-500"></span> Completos: {{ $sumCom }}</div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-yellow-400"></span> Vacantes: {{ $sumVac }}</div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-500"></span> Excedidos: {{ $sumExc }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end p-3 border-t dark:border-gray-700">
                    <button data-modal-hide="modalSubsistema"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-900 bg-gray-100 rounded-lg
                       hover:bg-gray-200 focus:outline-none focus:ring-4 focus:ring-gray-300
                       dark:text-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-800">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Helpers para ocultar scrollbar fino en contenedores horizontales --}}
    <style>
        .no-scrollbar::-webkit-scrollbar{display:none}
        .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
    </style>

    <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

    {{-- JS de multiselect (buscar / seleccionar todo / filtrados / limpiar / chips) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-multicheck]').forEach(root => {
                const inputSearch     = root.querySelector('[data-search]');
                const list            = root.querySelector('[data-list]');
                const btnClear        = root.querySelector('[data-clear]');
                const btnSelectAll    = root.querySelector('[data-select-all]');
                const btnSelectVis    = root.querySelector('[data-select-visible]');
                const btnApply        = root.querySelector('[data-apply]');
                const checks          = () => Array.from(list.querySelectorAll('input[type="checkbox"]'));
                const badges          = root.querySelector('[data-badges]');

                inputSearch?.addEventListener('input', e => {
                    const q = e.target.value.toLowerCase();
                    checks().forEach(chk => {
                        const li = chk.closest('li');
                        const txt = (chk.dataset.text || chk.value).toLowerCase();
                        li.style.display = txt.includes(q) ? '' : 'none';
                    });
                });

                btnSelectAll?.addEventListener('click', () => {
                    checks().forEach(c => c.checked = true);
                    renderBadges();
                });

                btnSelectVis?.addEventListener('click', () => {
                    checks().forEach(c => {
                        const li = c.closest('li');
                        if (li && li.style.display !== 'none') c.checked = true;
                    });
                    renderBadges();
                });

                btnClear?.addEventListener('click', () => {
                    checks().forEach(c => c.checked = false);
                    inputSearch && (inputSearch.value = '');
                    checks().forEach(c => c.closest('li').style.display = '');
                    renderBadges();
                });

                btnApply?.addEventListener('click', () => {
                    const btn = root.querySelector('[data-dropdown-toggle]');
                    if (btn) btn.click();
                });

                const renderBadges = () => {
                    if (!badges) return;
                    const values = checks().filter(c => c.checked).map(c => c.dataset.text || c.value);
                    badges.innerHTML = values.length
                        ? values.slice(0,3).map(v => `<span class="mr-1 mb-1 inline-block text-[10px] px-2 py-0.5 rounded bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-100">${escapeHtml(v)}</span>`).join('') +
                        (values.length>3 ? `<span class="text-[10px] text-gray-500">+${values.length-3}</span>` : '')
                        : '<span class="text-[10px] text-gray-400">Sin selección</span>';
                };
                checks().forEach(c => c.addEventListener('change', renderBadges));
                renderBadges();
            });

            function escapeHtml(s){return s.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]))}

            // Donut Chart
            const ctx = document.getElementById('chartSubsistema');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: { labels: ['Completos','Vacantes','Excedidos'], datasets: [{ data: [{{ $sumCom }}, {{ $sumVac }}, {{ $sumExc }}] }] },
                    options: { plugins: { legend: { display: true } }, cutout: '60%' }
                });
            }
        });
    </script>
@endsection
