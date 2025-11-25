{{-- resources/views/seleccion/resultado.blade.php --}}
@extends('layouts.app')

@section('content')
    @php
        // Contadores
        $countCarrito     = count($carritoAptosCed ?? []) + count($carritoNoAptosCed ?? []);
        $countTablaTotal  = $usuarios->total();
        $countPagina      = $usuarios->count();
    @endphp

    <section class="bg-gray-50 dark:bg-gray-900 py-4">
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-8">

            {{-- Header + Acciones --}}
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Resultados</h1>
                <div class="flex gap-2">
                    <a href="{{ route('seleccion.opciones') }}"
                       class="px-3 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-sm text-gray-800 dark:text-gray-100">Volver</a>

                    <a href="{{ route('seleccion.resultados', array_merge(request()->query(), ['clear'=>1])) }}"
                       class="px-3 py-2 rounded-md bg-red-600 text-white text-sm hover:bg-red-700">Limpiar universo</a>

                    <a href="{{ route('seleccion.carrito') }}"
                       class="px-3 py-2 rounded-md bg-primary-700 text-white text-sm hover:bg-primary-800">Ver carrito</a>
                </div>
            </div>

            {{-- ===== Cards de contadores ===== --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
                <div class="rounded-2xl border border-emerald-200 dark:border-emerald-900/40 bg-white dark:bg-gray-800 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-semibold uppercase text-emerald-700 dark:text-emerald-300">En carrito</div>
                            <div class="text-3xl font-bold text-emerald-900 dark:text-emerald-100">
                                <span id="count-carrito">{{ $countCarrito }}</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-emerald-700 dark:text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 3h1.386c.51 0 .955.343 1.09.835l.383 1.436m0 0L6.75 12.75m-1.641-7.479h13.382c.958 0 1.636.924 1.39 1.853l-1.5 5.625a1.5 1.5 0 01-1.447 1.125H7.5m0 0A1.5 1.5 0 006 15.75h11.25m-9 3.375a.375.375 0 11-.75 0 .375.375 0 01.75 0zm9 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-gray-800 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">En tabla (total)</div>
                            <div class="text-3xl font-bold text-slate-900 dark:text-slate-100">
                                <span id="count-tabla">{{ $countTablaTotal }}</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-900/40 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-700 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5M6 3.75v16.5m4.5-16.5v16.5m4.5-16.5v16.5" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-indigo-200 dark:border-indigo-900/40 bg-white dark:bg-gray-800 p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-semibold uppercase text-indigo-700 dark:text-indigo-300">En página</div>
                            <div class="text-3xl font-bold text-indigo-900 dark:text-indigo-100">
                                <span id="count-pagina">{{ $countPagina }}</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-700 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.64 0 8.577 3.01 9.964 7.178.07.213.07.431 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.64 0-8.577-3.01-9.964-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Filtros ===== --}}
            <form method="GET" action="{{ route('seleccion.resultados') }}" class="mb-4 grid grid-cols-1 md:grid-cols-6 gap-3">
                <input type="hidden" name="cedulas" value="{{ implode(',', $cedulas) }}">

                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Buscar</label>
                    <input type="text" name="q" value="{{ $q }}"
                           class="w-full rounded-md border dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none"
                           placeholder="Cédula, nombre, grado...">
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Estado</label>
                    <select name="estado"
                            class="w-full rounded-md border dark:border-gray-600 bg-white dark:bg-gray-700 text-sm">
                        <option value="todos" @selected(($estadoSeleccionado ?? 'todos')==='todos')>Todos</option>
                        <option value="activo" @selected(($estadoSeleccionado ?? 'todos')==='activo')>Solo ACTIVO</option>
                        <option value="alerta" @selected(($estadoSeleccionado ?? 'todos')==='alerta')>Con alertas</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Selección</label>
                    <select name="seleccion"
                            class="w-full rounded-md border dark:border-gray-600 bg-white dark:bg-gray-700 text-sm">
                        <option value="todos" @selected(($seleccionFiltro ?? 'todos')==='todos')>Todos</option>
                        <option value="seleccionados" @selected(($seleccionFiltro ?? 'todos')==='seleccionados')>Seleccionados</option>
                        <option value="no_seleccionados" @selected(($seleccionFiltro ?? 'todos')==='no_seleccionados')>No seleccionados</option>
                    </select>
                </div>

                {{-- Proyección licencia --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Proyección licencia</label>
                    <button id="btn-proylic" type="button" data-dropdown-toggle="dd-proylic"
                            class="inline-flex w-full items-center justify-between rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
          <span class="truncate">Proyección licencia
            <span id="badge-proylic" class="ml-1 inline-flex items-center rounded-full bg-gray-200 dark:bg-gray-600 px-2 py-0.5 text-[11px] font-semibold text-gray-700 dark:text-gray-200">
              {{ count($proyLicSeleccion ?? []) }}
            </span>
          </span>
                        <svg class="ml-2 h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div id="dd-proylic" class="z-20 hidden w-80 max-w-[90vw] rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3 shadow-xl">
                        <label for="proylic-search" class="sr-only">Buscar</label>
                        <input id="proylic-search" type="text" placeholder="Buscar..."
                               class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm focus:outline-none">
                        <ul id="proylic-list" class="mt-2 max-h-60 overflow-y-auto space-y-2 pr-1">
                            @forelse(($proyeccionOpciones ?? []) as $opt)
                                @php $checked = in_array($opt, ($proyLicSeleccion ?? []), true); @endphp
                                <li class="proylic-item" data-text="{{ mb_strtolower($opt,'UTF-8') }}">
                                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-0"
                                               name="proyeccion_licencia[]" value="{{ $opt }}" @checked($checked)>
                                        <span class="truncate" title="{{ $opt }}">{{ $opt }}</span>
                                    </label>
                                </li>
                            @empty
                                <li class="text-[12px] text-gray-500 dark:text-gray-400 px-1 py-1">No hay opciones</li>
                            @endforelse
                        </ul>
                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" id="proylic-select-all" class="text-[12px] font-medium text-primary-700 hover:underline dark:text-primary-400">Seleccionar todo</button>
                            <button type="button" id="proylic-clear" class="text-[12px] text-gray-600 hover:underline dark:text-gray-300">Limpiar</button>
                        </div>
                    </div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Se envían como <code>proyeccion_licencia[]</code>.</div>
                </div>

                {{-- Alertas --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Alertas</label>
                    <button id="btn-alertas" type="button" data-dropdown-toggle="dd-alertas"
                            class="inline-flex w-full items-center justify-between rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
          <span class="truncate">Columnas de alertas
            <span id="badge-alertas" class="ml-1 inline-flex items-center rounded-full bg-gray-200 dark:bg-gray-600 px-2 py-0.5 text-[11px] font-semibold text-gray-700 dark:text-gray-200">
              {{ count($alertasSeleccionadas ?? []) }}
            </span>
          </span>
                        <svg class="ml-2 h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div id="dd-alertas" class="z-20 hidden w-[28rem] max-w-[90vw] rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3 shadow-xl">
                        <label for="alertas-search" class="sr-only">Buscar</label>
                        <input id="alertas-search" type="text" placeholder="Buscar columna..."
                               class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm focus:outline-none">
                        <ul id="alertas-list" class="mt-2 max-h-60 overflow-y-auto space-y-2 pr-1">
                            @forelse(($opcionesAlertas ?? []) as $col)
                                @php $checked = in_array($col, ($alertasSeleccionadas ?? []), true); $label = str_replace('_',' ', $col); @endphp
                                <li class="alertas-item" data-text="{{ mb_strtolower($label,'UTF-8') }}">
                                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-0"
                                               name="alertas[]" value="{{ $col }}" @checked($checked)>
                                        <span class="truncate uppercase" title="{{ $label }}">{{ $label }}</span>
                                    </label>
                                </li>
                            @empty
                                <li class="text-[12px] text-gray-500 dark:text-gray-400 px-1 py-1">No hay columnas de alerta</li>
                            @endforelse
                        </ul>
                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" id="alertas-select-all" class="text-[12px] font-medium text-primary-700 hover:underline dark:text-primary-400">Seleccionar todo</button>
                            <button type="button" id="alertas-clear" class="text-[12px] text-gray-600 hover:underline dark:text-gray-300">Limpiar</button>
                        </div>
                    </div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Se envían como <code>alertas[]</code>.</div>
                </div>

                {{-- Fecha efectiva --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Fecha efectiva</label>
                    <button id="btn-fechaef" type="button" data-dropdown-toggle="dd-fechaef"
                            class="inline-flex w-full items-center justify-between rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
          <span class="truncate">Fecha efectiva
            <span id="badge-fechaef" class="ml-1 inline-flex items-center rounded-full bg-gray-200 dark:bg-gray-600 px-2 py-0.5 text-[11px] font-semibold text-gray-700 dark:text-gray-200">
              {{ count($fechaEfSeleccion ?? []) }}
            </span>
          </span>
                        <svg class="ml-2 h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div id="dd-fechaef" class="z-20 hidden w-[26rem] max-w-[90vw] rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3 shadow-xl">
                        <label for="fechaef-search" class="sr-only">Buscar</label>
                        <input id="fechaef-search" type="text" placeholder="Buscar fecha (YYYY-MM-DD)..."
                               class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm focus:outline-none">
                        <ul id="fechaef-list" class="mt-2 max-h-60 overflow-y-auto space-y-2 pr-1">
                            @forelse(($fechaEfOpciones ?? []) as $opt)
                                @php $label = $opt; $checked = in_array($opt, ($fechaEfSeleccion ?? []), true); @endphp
                                <li class="fechaef-item" data-text="{{ mb_strtolower($label,'UTF-8') }}">
                                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-0"
                                               name="fecha_efectiva[]" value="{{ $opt }}" @checked($checked)>
                                        <span class="truncate" title="{{ $label }}">{{ $label }}</span>
                                    </label>
                                </li>
                            @empty
                                <li class="text-[12px] text-gray-500 dark:text-gray-400 px-1 py-1">No hay fechas</li>
                            @endforelse
                        </ul>
                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" id="fechaef-select-all" class="text-[12px] font-medium text-primary-700 hover:underline dark:text-primary-400">Seleccionar todo</button>
                            <button type="button" id="fechaef-clear" class="text-[12px] text-gray-600 hover:underline dark:text-gray-300">Limpiar</button>
                        </div>
                    </div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Se envían como <code>fecha_efectiva[]</code>.</div>
                </div>

                {{-- NUEVO: Estado efectivo --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Estado efectivo</label>
                    <button id="btn-estef" type="button" data-dropdown-toggle="dd-estef"
                            class="inline-flex w-full items-center justify-between rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
          <span class="truncate">Estado efectivo
            <span id="badge-estef" class="ml-1 inline-flex items-center rounded-full bg-gray-200 dark:bg-gray-600 px-2 py-0.5 text-[11px] font-semibold text-gray-700 dark:text-gray-200">
              {{ count($estadoEfSeleccion ?? []) }}
            </span>
          </span>
                        <svg class="ml-2 h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div id="dd-estef" class="z-20 hidden w-[26rem] max-w-[90vw] rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3 shadow-xl">
                        <label for="estef-search" class="sr-only">Buscar</label>
                        <input id="estef-search" type="text" placeholder="Buscar estado..."
                               class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm focus:outline-none">
                        <ul id="estef-list" class="mt-2 max-h-60 overflow-y-auto space-y-2 pr-1">
                            @forelse(($estadoEfOpciones ?? []) as $opt)
                                @php $checked = in_array($opt, ($estadoEfSeleccion ?? []), true); @endphp
                                <li class="estef-item" data-text="{{ mb_strtolower($opt,'UTF-8') }}">
                                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-0"
                                               name="estado_efectivo[]" value="{{ $opt }}" @checked($checked)>
                                        <span class="truncate" title="{{ $opt }}">{{ $opt }}</span>
                                    </label>
                                </li>
                            @empty
                                <li class="text-[12px] text-gray-500 dark:text-gray-400 px-1 py-1">Sin estados</li>
                            @endforelse
                        </ul>
                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" id="estef-select-all" class="text-[12px] font-medium text-primary-700 hover:underline dark:text-primary-400">Seleccionar todo</button>
                            <button type="button" id="estef-clear" class="text-[12px] text-gray-600 hover:underline dark:text-gray-300">Limpiar</button>
                        </div>
                    </div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Se envían como <code>estado_efectivo[]</code>.</div>
                </div>

                {{-- NUEVO: Nomenclatura efectiva --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Nomenclatura efectiva</label>
                    <button id="btn-nomef" type="button" data-dropdown-toggle="dd-nomef"
                            class="inline-flex w-full items-center justify-between rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
          <span class="truncate">Nomenclatura efectiva
            <span id="badge-nomef" class="ml-1 inline-flex items-center rounded-full bg-gray-200 dark:bg-gray-600 px-2 py-0.5 text-[11px] font-semibold text-gray-700 dark:text-gray-200">
              {{ count($nomenSeleccion ?? []) }}
            </span>
          </span>
                        <svg class="ml-2 h-4 w-4 opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div id="dd-nomef" class="z-20 hidden w-[32rem] max-w-[95vw] rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3 shadow-xl">
                        <label for="nomef-search" class="sr-only">Buscar</label>
                        <input id="nomef-search" type="text" placeholder="Buscar unidad / nomenclatura..."
                               class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1.5 text-sm focus:outline-none">
                        <ul id="nomef-list" class="mt-2 max-h-60 overflow-y-auto space-y-2 pr-1">
                            @forelse(($nomenOpciones ?? []) as $opt)
                                @php $checked = in_array($opt, ($nomenSeleccion ?? []), true); @endphp
                                <li class="nomef-item" data-text="{{ mb_strtolower($opt,'UTF-8') }}">
                                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                        <input type="checkbox" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-0"
                                               name="nomenclatura_efectiva[]" value="{{ $opt }}" @checked($checked)>
                                        <span class="truncate" title="{{ $opt }}">{{ $opt }}</span>
                                    </label>
                                </li>
                            @empty
                                <li class="text-[12px] text-gray-500 dark:text-gray-400 px-1 py-1">Sin nomenclaturas</li>
                            @endforelse
                        </ul>
                        <div class="mt-3 flex items-center justify-between">
                            <button type="button" id="nomef-select-all" class="text-[12px] font-medium text-primary-700 hover:underline dark:text-primary-400">Seleccionar todo</button>
                            <button type="button" id="nomef-clear" class="text-[12px] text-gray-600 hover:underline dark:text-gray-300">Limpiar</button>
                        </div>
                    </div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Se envían como <code>nomenclatura_efectiva[]</code>.</div>
                </div>

                <div class="md:col-span-6">
                    <button class="px-3 py-2 rounded-md bg-gray-900 text-white text-sm hover:bg-black">Aplicar</button>
                </div>
            </form>

            {{-- Leyenda rápida --}}
            <div class="mb-3 text-[11px] text-gray-600 dark:text-gray-300 flex flex-wrap gap-2 items-center">
                <span class="font-semibold">Leyenda:</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200">Enf. catastrófica</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200">Discapacidad</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200">Contrato estudios</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-200">Problemas salud / varias</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-200">Devengación</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-pink-100 text-pink-800 dark:bg-pink-900/40 dark:text-pink-200">Maternidad</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200">Pase UCP/CCP/CPL</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-800 dark:bg-slate-900/40 dark:text-slate-200">Novedad situación</span>
            </div>

            {{-- ===== Tabla resultados ===== --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-3 py-2">Cédula</th>
                        <th class="px-3 py-2">Apellidos Nombres</th>
                        <th class="px-3 py-2">Grado</th>
                        <th class="px-3 py-2">Alertas</th>
                        <th class="px-3 py-2">Estado</th>
                        <th class="px-3 py-2 text-right">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($usuarios as $u)
                        @php
                            $norm = function($v){
                              if ($v === null) return false;
                              $s = trim((string)$v);
                              if ($s === '') return false;
                              $up = mb_strtoupper($s,'UTF-8');
                              return !in_array($up, ['NO','N/A','NA','0'], true);
                            };
                            $cls = [
                              'red'    => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
                              'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200',
                              'orange' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-200',
                              'indigo' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200',
                              'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-200',
                              'pink'   => 'bg-pink-100 text-pink-800 dark:bg-pink-900/40 dark:text-pink-200',
                              'blue'   => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
                              'slate'  => 'bg-slate-100 text-slate-800 dark:bg-slate-900/40 dark:text-slate-200',
                              'gray'   => 'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-200',
                              'green'  => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
                            ];
                            $alerts = [];
                            if ($norm($u->contrato_estudios ?? null))           $alerts[] = ['label'=>'Contrato Estudios','color'=>'indigo','text'=>$u->contrato_estudios ?? ''];
                            if ($norm($u->enf_catast_sp ?? null))                $alerts[] = ['label'=>'Enf. Catastrófica (SP)','color'=>'red','text'=>$u->enf_catast_sp ?? ''];
                            if ($norm($u->enf_catast_conyuge_hijos ?? null))     $alerts[] = ['label'=>'Enf. Catastrófica (Núcleo)','color'=>'red','text'=>$u->enf_catast_conyuge_hijos ?? ''];
                            if ($norm($u->discapacidad_sp ?? null))              $alerts[] = ['label'=>'Discapacidad (SP)','color'=>'yellow','text'=>$u->discapacidad_sp ?? ''];
                            if ($norm($u->discapacidad_conyuge_hijos ?? null))   $alerts[] = ['label'=>'Discapacidad (Núcleo)','color'=>'yellow','text'=>$u->discapacidad_conyuge_hijos ?? ''];
                            if ($norm($u->alertas ?? null))                      $alerts[] = ['label'=>'Alertas','color'=>'orange','text'=>$u->alertas ?? ''];
                            if ($norm($u->alertas_problemas_salud ?? null))      $alerts[] = ['label'=>'Problemas de Salud','color'=>'orange','text'=>$u->alertas_problemas_salud ?? ''];
                            $nov = trim((string)($u->novedad_situacion ?? ''));
                            if ($nov !== '' && mb_strtoupper($nov,'UTF-8') !== 'ACTIVO' && $norm($nov)) {
                                $alerts[] = ['label'=>'Novedad Situación','color'=>'slate','text'=>$nov];
                            }
                            if ($norm($u->alerta_devengacion ?? null))           $alerts[] = ['label'=>'Devengación','color'=>'purple','text'=>$u->alerta_devengacion ?? ''];
                            if ($norm($u->alerta_marco_legal ?? null))           $alerts[] = ['label'=>'Marco Legal','color'=>'purple','text'=>$u->alerta_marco_legal ?? ''];
                            if ($norm($u->observacion_tenencia ?? null))         $alerts[] = ['label'=>'Obs. Tenencia','color'=>'gray','text'=>$u->observacion_tenencia ?? ''];
                            if ($norm($u->pase_ucp_ccp_cpl ?? null))             $alerts[] = ['label'=>'Pase UCP/CCP/CPL','color'=>'blue','text'=>$u->pase_ucp_ccp_cpl ?? ''];
                            $mat = $u->FaseMaternidadUDGA ?? ($u->fase_maternidad ?? ($u->maternidad ?? null));
                            if ($norm($mat))                                     $alerts[] = ['label'=>'Maternidad','color'=>'pink','text'=>$mat];

                            $enApto   = in_array($u->cedula, $carritoAptosCed ?? []);
                            $enNoApto = in_array($u->cedula, $carritoNoAptosCed ?? []);
                        @endphp

                        <tr class="border-b dark:border-gray-700 align-top" id="row-{{ $u->cedula }}" data-cedula="{{ $u->cedula }}">
                            <td class="px-3 py-2 font-mono">{{ $u->cedula }}</td>
                            <td class="px-3 py-2">{{ $u->apellidos_nombres }}</td>
                            <td class="px-3 py-2">{{ $u->grado }}</td>

                            <td class="px-3 py-2">
                                @if(empty($alerts))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">Sin alertas</span>
                                @else
                                    <div class="flex flex-wrap">
                                        @foreach($alerts as $al)
                                            <div class="relative inline-block group mr-1 mb-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $cls[$al['color']] ?? $cls['gray'] }}">
                          {{ $al['label'] }}
                        </span>
                                                @php $txt = trim((string)($al['text'] ?? '')); @endphp
                                                @if($txt !== '')
                                                    <div class="absolute left-0 mt-1 hidden group-hover:block z-20 w-72 p-3 rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl">
                                                        <div class="text-[11px] font-semibold mb-1">{{ $al['label'] }}</div>
                                                        <div class="text-[12px] whitespace-pre-wrap break-words">{{ $txt }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td class="px-3 py-2" id="status-{{ $u->cedula }}">
                                @if($enApto)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-green-100 text-green-800">EN CARRITO: APTO</span>
                                @elseif($enNoApto)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-red-100 text-red-800">EN CARRITO: NO APTO</span>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400">—</span>
                                @endif
                            </td>

                            <td class="px-3 py-2 text-right">
                                @php $disabled = ($enApto || $enNoApto) ? 'disabled' : ''; @endphp
                                <div class="inline-flex gap-2">
                                    <button class="px-3 py-1 rounded-md text-white text-xs {{ $enApto || $enNoApto ? 'bg-green-600/60 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700' }}"
                                            data-calificar data-estado="APTO" data-cedula="{{ $u->cedula }}" {{ $disabled }}>
                                        APTO
                                    </button>
                                    <button class="px-3 py-1 rounded-md text-white text-xs {{ $enApto || $enNoApto ? 'bg-red-600/60 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700' }}"
                                            data-calificar data-estado="NO_APTO" data-cedula="{{ $u->cedula }}" {{ $disabled }}>
                                        NO APTO
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">Sin registros</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                    Mostrando {{ $usuarios->firstItem() ?? 0 }}–{{ $usuarios->lastItem() ?? 0 }} de {{ $usuarios->total() }} resultados
                </div>
                {{ $usuarios->links() }}
            </div>

        </div>
    </section>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).content;
            const URL_CAL = "{{ route('seleccion.calificar') }}";

            function incrementCounter(id, delta) {
                const el = document.getElementById(id);
                if (!el) return;
                const current = parseInt((el.textContent || '0').replace(/\D/g,''), 10) || 0;
                el.textContent = current + delta;
            }

            function markEnCarrito(cedula, estado, shouldIncrement = true) {
                const status = document.getElementById('status-' + cedula);
                if (status) {
                    status.innerHTML = '';
                    const span = document.createElement('span');
                    span.className = 'inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold ' +
                        (estado === 'APTO' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                    span.textContent = 'EN CARRITO: ' + (estado === 'APTO' ? 'APTO' : 'NO APTO');
                    status.appendChild(span);
                }
                const row = document.getElementById('row-' + cedula);
                if (row) {
                    row.querySelectorAll('[data-calificar]').forEach(btn => {
                        btn.setAttribute('disabled', 'disabled');
                        if (btn.getAttribute('data-estado') === 'APTO') {
                            btn.classList.remove('bg-green-600','hover:bg-green-700');
                            btn.classList.add('bg-green-600/60','cursor-not-allowed');
                        } else {
                            btn.classList.remove('bg-red-600','hover:bg-red-700');
                            btn.classList.add('bg-red-600/60','cursor-not-allowed');
                        }
                    });
                }
                if (shouldIncrement) incrementCounter('count-carrito', 1);
            }

            async function calificar(cedula, estado) {
                let novedad = 'SIN_NOVEDAD';
                let detalle = null;
                if (estado === 'NO_APTO') {
                    const tieneNovedad = confirm('¿Marcar con NOVEDAD? (Aceptar = Sí, Cancelar = No)');
                    if (tieneNovedad) {
                        novedad = 'NOVEDAD';
                        detalle = prompt('Ingrese el detalle de la novedad (obligatorio):', '') || '';
                        if (!detalle.trim()) { alert('Debes ingresar el detalle de la novedad.'); return; }
                    }
                }
                try {
                    const res = await fetch(URL_CAL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': CSRF,'Accept':'application/json' },
                        body: JSON.stringify({ cedula, estado, novedad, detalle_novedad: detalle })
                    });
                    const data = await res.json();
                    if (data.status === 'ok') { markEnCarrito(cedula, estado, true); alert(data.message || 'Registrado'); }
                    else if (data.status === 'exists') { markEnCarrito(cedula, estado, false); alert(data.message || 'Ya estaba registrado'); }
                    else { alert(data.message || 'No se pudo registrar.'); }
                } catch { alert('Error de red o servidor.'); }
            }

            document.querySelectorAll('[data-calificar]').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (btn.hasAttribute('disabled')) return;
                    calificar(btn.getAttribute('data-cedula'), btn.getAttribute('data-estado'));
                });
            });

            // ---------- Utilidades de dropdown con checkboxes ----------
            function setupDropdown({btnId, ddId, listId, searchId, badgeId, selectAllId, clearId, itemClass}) {
                const btn = document.getElementById(btnId);
                const dd  = document.getElementById(ddId);
                const list= document.getElementById(listId);
                const search = document.getElementById(searchId);
                const badge = document.getElementById(badgeId);
                const selAll= document.getElementById(selectAllId);
                const clear = document.getElementById(clearId);

                function updateBadge() {
                    const n = list ? list.querySelectorAll('input[type="checkbox"]:checked').length : 0;
                    if (badge) badge.textContent = n;
                }

                if (search && list) {
                    search.addEventListener('input', () => {
                        const q = (search.value || '').toLowerCase().trim();
                        list.querySelectorAll('.' + itemClass).forEach(li => {
                            const txt = (li.getAttribute('data-text') || '').toLowerCase();
                            li.style.display = txt.includes(q) ? '' : 'none';
                        });
                    });
                }

                list?.addEventListener('change', e => {
                    if (e.target && e.target.matches('input[type="checkbox"]')) updateBadge();
                });

                selAll?.addEventListener('click', () => {
                    list?.querySelectorAll('.' + itemClass).forEach(li => {
                        if (li.style.display === 'none') return;
                        const cb = li.querySelector('input[type="checkbox"]');
                        if (cb && !cb.checked) cb.checked = true;
                    });
                    updateBadge();
                });

                clear?.addEventListener('click', () => {
                    list?.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                    updateBadge();
                });

                // Fallback si Flowbite no está cargado
                if (btn && dd && typeof window?.Flowbite === 'undefined') {
                    btn.addEventListener('click', e => { e.preventDefault(); dd.classList.toggle('hidden'); });
                    document.addEventListener('click', e => {
                        if (!dd.classList.contains('hidden') && !dd.contains(e.target) && !btn.contains(e.target)) dd.classList.add('hidden');
                    });
                }

                updateBadge();
            }

            // Proyección licencia
            setupDropdown({
                btnId:'btn-proylic', ddId:'dd-proylic', listId:'proylic-list', searchId:'proylic-search',
                badgeId:'badge-proylic', selectAllId:'proylic-select-all', clearId:'proylic-clear', itemClass:'proylic-item'
            });

            // Alertas
            setupDropdown({
                btnId:'btn-alertas', ddId:'dd-alertas', listId:'alertas-list', searchId:'alertas-search',
                badgeId:'badge-alertas', selectAllId:'alertas-select-all', clearId:'alertas-clear', itemClass:'alertas-item'
            });

            // Fecha efectiva
            setupDropdown({
                btnId:'btn-fechaef', ddId:'dd-fechaef', listId:'fechaef-list', searchId:'fechaef-search',
                badgeId:'badge-fechaef', selectAllId:'fechaef-select-all', clearId:'fechaef-clear', itemClass:'fechaef-item'
            });

            // NUEVO: Estado efectivo
            setupDropdown({
                btnId:'btn-estef', ddId:'dd-estef', listId:'estef-list', searchId:'estef-search',
                badgeId:'badge-estef', selectAllId:'estef-select-all', clearId:'estef-clear', itemClass:'estef-item'
            });

            // NUEVO: Nomenclatura efectiva
            setupDropdown({
                btnId:'btn-nomef', ddId:'dd-nomef', listId:'nomef-list', searchId:'nomef-search',
                badgeId:'badge-nomef', selectAllId:'nomef-select-all', clearId:'nomef-clear', itemClass:'nomef-item'
            });
        });
    </script>
@endsection
