@extends('layouts.app')

@section('content')
    <div class="p-4 max-w-screen-2xl mx-auto dark:text-white">

        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
            Reporte orgánico — Visualizador
        </h2>

        {{-- =========================== FILTROS =========================== --}}
        <form method="GET" class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-6 items-end">

            {{-- Servicio (multi con checkboxes + buscador) --}}
            @php $selServicio = (array) request('servicio', []); @endphp
            <div data-dd class="relative">
                <label class="block text-xs font-semibold mb-1">Servicio</label>
                <button type="button" data-dd-btn
                        class="w-full inline-flex items-center justify-between rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                    <span class="truncate" data-dd-label>Selecciona servicios</span>
                    <span class="ml-2 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded bg-blue-600 text-white text-xs hidden" data-dd-count></span>
                </button>

                <div data-dd-panel
                     class="hidden absolute z-40 mt-1 w-[min(28rem,90vw)] rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-lg">
                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                        <input type="text" data-dd-search placeholder="Buscar servicio..."
                               class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1.5 text-sm">
                    </div>
                    <div class="max-h-64 overflow-y-auto text-sm" data-dd-list>
                        @foreach($opcionesServicio as $opt)
                            @php $checked = in_array($opt, $selServicio, true); @endphp
                            <label class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                                <input type="checkbox" name="servicio[]" value="{{ $opt }}" @checked($checked)
                                class="rounded border-gray-300 dark:border-gray-600">
                                <span class="truncate" title="{{ $opt }}">{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="p-2 border-t border-gray-200 dark:border-gray-700 flex items-center gap-2">
                        <button type="button" data-dd-select-all class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">Seleccionar todo</button>
                        <button type="button" data-dd-clear class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">Limpiar</button>
                        <span class="ml-auto text-[11px] text-gray-500 dark:text-gray-400">Enter/Esc para cerrar</span>
                    </div>
                </div>
            </div>

            {{-- Nomenclatura --}}
            @php $selNom = (array) request('nomenclatura', []); @endphp
            <div data-dd class="relative">
                <label class="block text-xs font-semibold mb-1">Nomenclatura</label>
                <button type="button" data-dd-btn
                        class="w-full inline-flex items-center justify-between rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                    <span class="truncate" data-dd-label>Selecciona nomenclaturas</span>
                    <span class="ml-2 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded bg-blue-600 text-white text-xs hidden" data-dd-count></span>
                </button>

                <div data-dd-panel
                     class="hidden absolute z-40 mt-1 w-[min(28rem,90vw)] rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-lg">
                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                        <input type="text" data-dd-search placeholder="Buscar nomenclatura..."
                               class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1.5 text-sm">
                    </div>
                    <div class="max-h-64 overflow-y-auto text-sm" data-dd-list>
                        @foreach($opcionesNomenclatura as $opt)
                            @php $checked = in_array($opt, $selNom, true); @endphp
                            <label class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                                <input type="checkbox" name="nomenclatura[]" value="{{ $opt }}" @checked($checked)
                                class="rounded border-gray-300 dark:border-gray-600">
                                <span class="truncate" title="{{ $opt }}">{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="p-2 border-t border-gray-200 dark:border-gray-700 flex items-center gap-2">
                        <button type="button" data-dd-select-all class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">Seleccionar todo</button>
                        <button type="button" data-dd-clear class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">Limpiar</button>
                        <span class="ml-auto text-[11px] text-gray-500 dark:text-gray-400">Enter/Esc para cerrar</span>
                    </div>
                </div>
            </div>

            {{-- Cargo --}}
            @php $selCargo = (array) request('cargo', []); @endphp
            <div data-dd class="relative">
                <label class="block text-xs font-semibold mb-1">Cargo</label>
                <button type="button" data-dd-btn
                        class="w-full inline-flex items-center justify-between rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                    <span class="truncate" data-dd-label>Selecciona cargos</span>
                    <span class="ml-2 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded bg-blue-600 text-white text-xs hidden" data-dd-count></span>
                </button>

                <div data-dd-panel
                     class="hidden absolute z-40 mt-1 w-[min(28rem,90vw)] rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-lg">
                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                        <input type="text" data-dd-search placeholder="Buscar cargo..."
                               class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1.5 text-sm">
                    </div>
                    <div class="max-h-64 overflow-y-auto text-sm" data-dd-list>
                        @foreach($opcionesCargo as $opt)
                            @php $checked = in_array($opt, $selCargo, true); @endphp
                            <label class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                                <input type="checkbox" name="cargo[]" value="{{ $opt }}" @checked($checked)
                                class="rounded border-gray-300 dark:border-gray-600">
                                <span class="truncate" title="{{ $opt }}">{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="p-2 border-t border-gray-200 dark:border-gray-700 flex items-center gap-2">
                        <button type="button" data-dd-select-all class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">Seleccionar todo</button>
                        <button type="button" data-dd-clear class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">Limpiar</button>
                        <span class="ml-auto text-[11px] text-gray-500 dark:text-gray-400">Enter/Esc para cerrar</span>
                    </div>
                </div>
            </div>

            {{-- Estado (VACANTE/COMPLETO/EXCEDIDO) --}}
            @php $selEstado = (array) request('estado', []); $estados = ['VACANTE','COMPLETO','EXCEDIDO']; @endphp
            <div data-dd class="relative">
                <label class="block text-xs font-semibold mb-1">Estado</label>
                <button type="button" data-dd-btn
                        class="w-full inline-flex items-center justify-between rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                    <span class="truncate" data-dd-label>Selecciona estados</span>
                    <span class="ml-2 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded bg-blue-600 text-white text-xs hidden" data-dd-count></span>
                </button>

                <div data-dd-panel
                     class="hidden absolute z-40 mt-1 w-[min(22rem,90vw)] rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-lg">
                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                        <input type="text" data-dd-search placeholder="Buscar estado..."
                               class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1.5 text-sm">
                    </div>
                    <div class="max-h-52 overflow-y-auto text-sm" data-dd-list>
                        @foreach($estados as $opt)
                            @php $checked = in_array($opt, $selEstado, true); @endphp
                            <label class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                                <input type="checkbox" name="estado[]" value="{{ $opt }}" @checked($checked)
                                class="rounded border-gray-300 dark:border-gray-600">
                                <span>{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="p-2 border-t border-gray-200 dark:border-gray-700 flex items-center gap-2">
                        <button type="button" data-dd-select-all class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">Seleccionar todo</button>
                        <button type="button" data-dd-clear class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">Limpiar</button>
                        <span class="ml-auto text-[11px] text-gray-500 dark:text-gray-400">Enter/Esc para cerrar</span>
                    </div>
                </div>
            </div>

            {{-- Grado orgánico --}}
            @php $selGrado = (array) request('grado_organico', []); @endphp
            <div data-dd class="relative">
                <label class="block text-xs font-semibold mb-1">Grado orgánico</label>
                <button type="button" data-dd-btn
                        class="w-full inline-flex items-center justify-between rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                    <span class="truncate" data-dd-label>Selecciona grados</span>
                    <span class="ml-2 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded bg-blue-600 text-white text-xs hidden" data-dd-count></span>
                </button>

                <div data-dd-panel
                     class="hidden absolute z-40 mt-1 w-[min(24rem,90vw)] rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-lg">
                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                        <input type="text" data-dd-search placeholder="Buscar grado..."
                               class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1.5 text-sm">
                    </div>
                    <div class="max-h-64 overflow-y-auto text-sm" data-dd-list>
                        @foreach($opcionesGrado as $g)
                            @php $checked = in_array($g, $selGrado, true); @endphp
                            <label class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                                <input type="checkbox" name="grado_organico[]" value="{{ $g }}" @checked($checked)
                                class="rounded border-gray-300 dark:border-gray-600">
                                <span>{{ $g }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="p-2 border-t border-gray-200 dark:border-gray-700 flex items-center gap-2">
                        <button type="button" data-dd-select-all class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">Seleccionar todo</button>
                        <button type="button" data-dd-clear class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">Limpiar</button>
                        <span class="ml-auto text-[11px] text-gray-500 dark:text-gray-400">Enter/Esc para cerrar</span>
                    </div>
                </div>
            </div>

            {{-- Subsistema --}}
            @php $selSubs = (array) request('subsistema', []); @endphp
            <div data-dd class="relative">
                <label class="block text-xs font-semibold mb-1">Subsistema</label>
                <button type="button" data-dd-btn
                        class="w-full inline-flex items-center justify-between rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm">
                    <span class="truncate" data-dd-label>Selecciona subsistemas</span>
                    <span class="ml-2 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded bg-blue-600 text-white text-xs hidden" data-dd-count></span>
                </button>

                <div data-dd-panel
                     class="hidden absolute z-40 mt-1 w-[min(26rem,90vw)] rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-lg">
                    <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                        <input type="text" data-dd-search placeholder="Buscar subsistema..."
                               class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1.5 text-sm">
                    </div>
                    <div class="max-h-64 overflow-y-auto text-sm" data-dd-list>
                        @foreach($opcionesSubsistema as $s)
                            @php $checked = in_array($s, $selSubs, true); @endphp
                            <label class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                                <input type="checkbox" name="subsistema[]" value="{{ $s }}" @checked($checked)
                                class="rounded border-gray-300 dark:border-gray-600">
                                <span class="truncate" title="{{ $s }}">{{ $s }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="p-2 border-t border-gray-200 dark:border-gray-700 flex items-center gap-2">
                        <button type="button" data-dd-select-all class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">Seleccionar todo</button>
                        <button type="button" data-dd-clear class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">Limpiar</button>
                        <span class="ml-auto text-[11px] text-gray-500 dark:text-gray-400">Enter/Esc para cerrar</span>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="sm:col-span-2 lg:col-span-6 flex flex-wrap gap-2">
                <button type="submit"
                        class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md
                           bg-blue-600 text-white hover:bg-blue-700">
                    Filtrar
                </button>

                <a href="{{ route('reporte_organico.index') }}"
                   class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md
                      bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                    Limpiar
                </a>

                <a href="{{ route('reporte_organico.exportarExcel', request()->query()) }}"
                   class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md
                      bg-emerald-600 text-white hover:bg-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 14a1 1 0 011-1h3v-3h4v3h3a1 1 0 011 1v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2z"/>
                        <path d="M7 7h2V3h2v4h2l-3 3-3-3z"/>
                    </svg>
                    Descargar Excel
                </a>

                <button type="button" onclick="openSubsistemaModal()"
                        class="ml-auto inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-md
                           bg-indigo-600 text-white hover:bg-indigo-700">
                    Ver resumen por subsistema
                </button>
            </div>
        </form>

        {{-- =========================== RESUMEN =========================== --}}
        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                <div class="text-xs text-gray-500 dark:text-gray-300">Orgánico aprobado (suma)</div>
                <div class="text-xl font-semibold">{{ number_format($totales->total_aprobado ?? 0) }}</div>
            </div>
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                <div class="text-xs text-gray-500 dark:text-gray-300">Orgánico efectivo (suma)</div>
                <div class="text-xl font-semibold">{{ number_format($totales->total_efectivo ?? 0) }}</div>
            </div>
        </div>

        {{-- =========================== LEYENDA SEMÁFORO =========================== --}}
        <div class="mb-3 text-xs flex items-center gap-4">
        <span class="inline-flex items-center gap-2">
            <span class="inline-block h-2.5 w-2.5 rounded-full bg-green-500"></span> COMPLETO
        </span>
            <span class="inline-flex items-center gap-2">
            <span class="inline-block h-2.5 w-2.5 rounded-full bg-yellow-400"></span> VACANTE
        </span>
            <span class="inline-flex items-center gap-2">
            <span class="inline-block h-2.5 w-2.5 rounded-full bg-red-500"></span> EXCEDIDO
        </span>
        </div>

        {{-- =========================== TABLA RESULTADOS =========================== --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold">Resultados</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-100 dark:bg-gray-900/30">
                    <tr class="text-left">
                        <th class="px-3 py-2 font-semibold">Servicio</th>
                        <th class="px-3 py-2 font-semibold">Nomenclatura</th>
                        <th class="px-3 py-2 font-semibold">Cargo</th>
                        <th class="px-3 py-2 font-semibold">Grado(s)</th>
                        <th class="px-3 py-2 font-semibold text-right">Orgánico aprobado</th>
                        <th class="px-3 py-2 font-semibold text-right">Orgánico efectivo</th>
                        <th class="px-3 py-2 font-semibold text-center">Alertas</th>
                        <th class="px-3 py-2 font-semibold">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($datos as $fila)
                        @php
                            $noCumplen = (int)($fila->no_cumplen_grado ?? 0);
                            $aprobado  = (int)($fila->organico_aprobado ?? 0);
                            $efectivo  = (int)($fila->organico_efectivo ?? 0);

                            if ($efectivo < $aprobado) { $estadoLabel = 'VACANTE';  $dotClass = 'bg-yellow-400'; }
                            elseif ($efectivo == $aprobado) { $estadoLabel = 'COMPLETO'; $dotClass = 'bg-green-500'; }
                            else { $estadoLabel = 'EXCEDIDO'; $dotClass = 'bg-red-500'; }
                        @endphp
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-3 py-2">{{ $fila->servicio_organico }}</td>
                            <td class="px-3 py-2">{{ $fila->nomenclatura_organico }}</td>
                            <td class="px-3 py-2">{{ $fila->cargo_organico }}</td>
                            <td class="px-3 py-2">{{ $fila->grado_organico }}</td>

                            <td class="px-3 py-2 text-right">{{ $aprobado }}</td>

                            <td class="px-3 py-2 text-right">
                                <span class="inline-flex items-center gap-2 justify-end">
                                    <span>{{ $efectivo }}</span>
                                    <span class="inline-block h-2.5 w-2.5 rounded-full {{ $dotClass }}"
                                          title="{{ $estadoLabel }}"></span>
                                </span>
                            </td>

                            <td class="px-3 py-2 text-center">
                                @if($noCumplen > 0)
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-4 w-4 inline-block text-red-600 dark:text-red-400"
                                         viewBox="0 0 20 20" fill="currentColor"
                                         title="Hay {{ $noCumplen }} ocupante(s) que no cumplen el grado orgánico">
                                        <path fill-rule="evenodd"
                                              d="M8.257 3.099c.765-1.36 2.721-1.36 3.486 0l6.518 11.588c.75 1.334-.213 2.993-1.743 2.993H3.482c-1.53 0-2.493-1.659-1.743-2.993L8.257 3.1zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V8a1 1 0 011-1z"
                                              clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </td>

                            <td class="px-3 py-2">
                                <form method="GET" action="{{ route('reporte_organico.ocupantes') }}">
                                    <input type="hidden" name="nomenclatura" value="{{ $fila->nomenclatura_organico }}">
                                    <input type="hidden" name="cargo"        value="{{ $fila->cargo_organico }}">
                                    <button type="submit"
                                            class="inline-flex items-center px-2 py-1.5 text-xs rounded-md
                                                   bg-indigo-600 text-white hover:bg-indigo-700">
                                        Ver ocupantes
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-3 text-center text-gray-500 dark:text-white">
                                No hay resultados con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $datos->links() }}
            </div>
        </div>

    </div>

    {{-- =========================== MODAL RESUMEN POR SUBSISTEMA =========================== --}}
    <div id="modal-subsistema" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/60" onclick="toggleModal('modal-subsistema')"></div>

        <div class="relative z-10 w-[95vw] max-w-6xl max-h-[85vh] overflow-hidden rounded-xl
                bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold">Resumen por subsistema (con filtros aplicados)</h3>
                <button type="button" class="p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
                        onclick="toggleModal('modal-subsistema')" aria-label="Cerrar">✕</button>
            </div>

            <div class="p-4 overflow-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                    <div class="md:col-span-2">
                        <table class="min-w-full text-xs">
                            <thead class="bg-gray-100 dark:bg-gray-800/60">
                            <tr class="text-left">
                                <th class="px-3 py-2 font-semibold">Subsistema</th>
                                <th class="px-3 py-2 font-semibold text-right">Aprobado</th>
                                <th class="px-3 py-2 font-semibold text-right">Efectivo</th>
                                <th class="px-3 py-2 font-semibold text-right">Vacantes</th>
                                <th class="px-3 py-2 font-semibold text-right">Completos</th>
                                <th class="px-3 py-2 font-semibold text-right">Excedidos</th>
                                <th class="px-3 py-2 font-semibold text-right">Diferencia (Ef − Ap)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $totAp=$totEf=$totVac=$totCom=$totExc=$totDif=0; @endphp
                            @forelse($statsSubsistema as $s)
                                @php
                                    $totAp += (int)$s->total_aprobado; $totEf += (int)$s->total_efectivo;
                                    $totVac += (int)$s->cargos_vacantes; $totCom += (int)$s->cargos_completos;
                                    $totExc += (int)$s->cargos_excedidos; $totDif += (int)$s->diferencia_total;
                                @endphp
                                <tr class="border-t border-gray-200 dark:border-gray-700">
                                    <td class="px-3 py-2">{{ $s->subsistema ?? 'SIN SUBSISTEMA' }}</td>
                                    <td class="px-3 py-2 text-right">{{ $s->total_aprobado }}</td>
                                    <td class="px-3 py-2 text-right">{{ $s->total_efectivo }}</td>
                                    <td class="px-3 py-2 text-right">{{ $s->cargos_vacantes }}</td>
                                    <td class="px-3 py-2 text-right">{{ $s->cargos_completos }}</td>
                                    <td class="px-3 py-2 text-right">{{ $s->cargos_excedidos }}</td>
                                    <td class="px-3 py-2 text-right">{{ $s->diferencia_total }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-3 text-center text-gray-500 dark:text-gray-300">Sin datos.</td>
                                </tr>
                            @endforelse
                            </tbody>
                            @if(count($statsSubsistema))
                                <tfoot class="bg-gray-50 dark:bg-gray-800/50">
                                <tr class="font-semibold border-t border-gray-200 dark:border-gray-700">
                                    <td class="px-3 py-2">Totales</td>
                                    <td class="px-3 py-2 text-right">{{ $totAp }}</td>
                                    <td class="px-3 py-2 text-right">{{ $totEf }}</td>
                                    <td class="px-3 py-2 text-right">{{ $totVac }}</td>
                                    <td class="px-3 py-2 text-right">{{ $totCom }}</td>
                                    <td class="px-3 py-2 text-right">{{ $totExc }}</td>
                                    <td class="px-3 py-2 text-right">{{ $totDif }}</td>
                                </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    <div class="md:col-span-1">
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                            <div class="mb-2 flex items-center justify-between">
                                <h4 class="text-xs font-semibold">Gráfico por subsistema</h4>
                                <select id="metric-select"
                                        class="rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-[11px] px-1.5 py-1">
                                    <option value="total_efectivo" selected>Efectivo</option>
                                    <option value="total_aprobado">Aprobado</option>
                                    <option value="cargos_vacantes">Vacantes</option>
                                    <option value="cargos_completos">Completos</option>
                                    <option value="cargos_excedidos">Excedidos</option>
                                </select>
                            </div>
                            <div class="relative">
                                <canvas id="subsistemaPie" height="180"></canvas>
                                <p id="subsistemaPieEmpty" class="mt-2 text-xs text-gray-500 dark:text-gray-400 hidden">No hay datos para graficar.</p>
                            </div>
                        </div>
                        <p class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">Tip: cambia la métrica para ver la distribución.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="toggleModal('modal-subsistema')"
                        class="inline-flex items-center px-3 py-2 text-sm rounded-md
                           bg-gray-100 hover:bg-gray-200 text-gray-900
                           dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-white">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    {{-- ===== Helpers Modal ===== --}}
    <script>
        function toggleModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.toggle('hidden');
            el.classList.toggle('flex');
        }
        function openSubsistemaModal() {
            toggleModal('modal-subsistema');
            setTimeout(() => {
                const sel = document.getElementById('metric-select');
                const metric = sel?.value || 'total_efectivo';
                window.renderSubsistemaPie?.(metric);
            }, 60);
        }
    </script>

    {{-- ===== Dropdowns con checkboxes + buscador (JS nativo) ===== --}}
    <script>
        (function(){
            const DROPDOWN_SELECTOR = '[data-dd]';
            const openClassList = new Set();

            function closeAll(except=null){
                document.querySelectorAll(DROPDOWN_SELECTOR).forEach(dd=>{
                    if (dd===except) return;
                    const panel = dd.querySelector('[data-dd-panel]');
                    const btn   = dd.querySelector('[data-dd-btn]');
                    panel?.classList.add('hidden');
                    btn?.setAttribute('aria-expanded','false');
                    openClassList.delete(dd);
                });
            }

            function initDropdown(dd){
                const btn    = dd.querySelector('[data-dd-btn]');
                const panel  = dd.querySelector('[data-dd-panel]');
                const search = dd.querySelector('[data-dd-search]');
                const list   = dd.querySelector('[data-dd-list]');
                const count  = dd.querySelector('[data-dd-count]');
                const label  = dd.querySelector('[data-dd-label]');
                const selAll = dd.querySelector('[data-dd-select-all]');
                const clear  = dd.querySelector('[data-dd-clear]');

                if(!btn || !panel || !list) return;

                // toggle
                btn.addEventListener('click', ()=>{
                    const hidden = panel.classList.contains('hidden');
                    closeAll(dd);
                    panel.classList.toggle('hidden', !hidden);
                    btn.setAttribute('aria-expanded', hidden?'true':'false');
                    if(hidden){ openClassList.add(dd); search?.focus(); } else { openClassList.delete(dd); }
                });

                // outside click
                document.addEventListener('click', (e)=>{
                    if(!dd.contains(e.target)) {
                        panel.classList.add('hidden');
                        btn.setAttribute('aria-expanded','false');
                        openClassList.delete(dd);
                    }
                });

                // Esc / Enter to close
                dd.addEventListener('keydown', (e)=>{
                    if(e.key === 'Escape' || e.key === 'Enter'){
                        panel.classList.add('hidden');
                        btn.setAttribute('aria-expanded','false');
                        openClassList.delete(dd);
                        btn.focus();
                    }
                });

                // search filter
                search?.addEventListener('input', ()=>{
                    const q = (search.value || '').toLowerCase();
                    list.querySelectorAll('label').forEach(row=>{
                        const txt = (row.textContent || '').toLowerCase();
                        row.classList.toggle('hidden', !txt.includes(q));
                    });
                });

                // select all
                selAll?.addEventListener('click', ()=>{
                    list.querySelectorAll('input[type="checkbox"]:not(:checked):not(.hidden input)').forEach(chk=>{
                        if (!chk.closest('label')?.classList.contains('hidden')) chk.checked = true;
                    });
                    updateCount();
                });

                // clear
                clear?.addEventListener('click', ()=>{
                    list.querySelectorAll('input[type="checkbox"]:checked').forEach(chk=> chk.checked = false);
                    updateCount();
                });

                // change counter on any checkbox change
                list.addEventListener('change', updateCount);

                function updateCount(){
                    const n = list.querySelectorAll('input[type="checkbox"]:checked').length;
                    if(count){
                        count.textContent = n;
                        count.classList.toggle('hidden', n===0);
                    }
                    if(label){
                        const base = label.getAttribute('data-base') || label.textContent || 'Selecciona...';
                        // mantenemos el texto base, solo mostramos contador
                        label.setAttribute('data-base', base);
                    }
                }

                updateCount();
            }

            document.querySelectorAll(DROPDOWN_SELECTOR).forEach(initDropdown);

            // cerrar con Esc si cualquier dropdown está abierto
            document.addEventListener('keydown', (e)=>{
                if(e.key === 'Escape' && openClassList.size){
                    closeAll();
                }
            });
        })();
    </script>

    {{-- ===== Chart.js & gráfico ===== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const raw = @json($statsSubsistema ?? []);
            const data = raw.map(r => ({
                subsistema: r.subsistema ?? 'SIN SUBSISTEMA',
                total_aprobado: Number(r.total_aprobado ?? 0),
                total_efectivo: Number(r.total_efectivo ?? 0),
                cargos_vacantes: Number(r.cargos_vacantes ?? 0),
                cargos_completos: Number(r.cargos_completos ?? 0),
                cargos_excedidos: Number(r.cargos_excedidos ?? 0),
                diferencia_total: Number(r.diferencia_total ?? 0),
            }));

            let subsistemaPieChart = null;
            function currentTextColor() {
                const isDark = document.documentElement.classList.contains('dark');
                return isDark ? '#e5e7eb' : '#374151';
            }
            function genColors(n) {
                const arr = [];
                for (let i = 0; i < n; i++) {
                    const hue = Math.round((360 / Math.max(1, n)) * i);
                    arr.push(`hsl(${hue} 70% 55%)`);
                }
                return arr;
            }
            function buildDataset(metricKey) {
                const labels = data.map(d => d.subsistema);
                const values = data.map(d => d[metricKey] ?? 0);
                const total = values.reduce((a,b) => a + b, 0);
                const hasData = total > 0;
                return { labels, values, hasData };
            }
            function titleForMetric(metricKey) {
                switch (metricKey) {
                    case 'total_aprobado': return 'Aprobado';
                    case 'total_efectivo': return 'Efectivo';
                    case 'cargos_vacantes': return 'Vacantes';
                    case 'cargos_completos': return 'Completos';
                    case 'cargos_excedidos': return 'Excedidos';
                    default: return 'Distribución';
                }
            }
            window.renderSubsistemaPie = function(metricKey = 'total_efectivo') {
                const { labels, values, hasData } = buildDataset(metricKey);
                const canvas = document.getElementById('subsistemaPie');
                const empty  = document.getElementById('subsistemaPieEmpty');
                if (!canvas) return;

                if (!hasData) {
                    canvas.classList.add('hidden'); empty?.classList.remove('hidden');
                    if (subsistemaPieChart) { subsistemaPieChart.destroy(); subsistemaPieChart = null; }
                    return;
                } else {
                    canvas.classList.remove('hidden'); empty?.classList.add('hidden');
                }

                const ctx = canvas.getContext('2d');
                const colors = genColors(labels.length);
                const labelColor = currentTextColor();

                if (subsistemaPieChart) subsistemaPieChart.destroy();

                subsistemaPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: { labels, datasets: [{ data: values, backgroundColor: colors, borderColor: 'transparent' }] },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom', labels: { color: labelColor, boxWidth: 12, font: { size: 10 } } },
                            tooltip: { callbacks: {
                                    label: (ctx) => {
                                        const total = ctx.dataset.data.reduce((a,b)=>a+b,0) || 1;
                                        const val = Number(ctx.raw) || 0;
                                        const pct = (val * 100 / total).toFixed(1);
                                        return ` ${ctx.label}: ${val} (${pct}%)`;
                                    }
                                }},
                            title: { display: true, text: `Distribución — ${titleForMetric(metricKey)}`, color: labelColor, font: { size: 12, weight: '600' } }
                        }
                    }
                });
            };
            document.getElementById('metric-select')?.addEventListener('change', (e) => {
                window.renderSubsistemaPie(e.target.value);
            });
        })();
    </script>
@endsection
