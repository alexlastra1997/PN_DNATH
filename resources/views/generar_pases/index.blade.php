@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 py-3 sm:py-5">
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-12">

            {{-- ====== Título ====== --}}
            <div class="mb-4">
                <h1 class="text-lg font-semibold text-gray-800 ">Filtrar una Base</h1>
                <p class="text-xs text-gray-500 ">Filtra y define requerimientos por grado.</p>
            </div>

            @php
                $promSel        = $promSel        ?? [];
                $provTrabSel    = $provTrabSel    ?? [];
                $flagsSel       = $flagsSel       ?? [];
                $alertsSelKeys  = $alertsSelKeys  ?? [];
                $fechaSel       = $fechaSel       ?? [];
                $nomenclaturaSel= $nomSel         ?? [];
                $funcionSel     = $funSel         ?? [];
                $estadoSel      = $estSel         ?? [];
            @endphp

            <form method="GET" action="{{ route('generar_pases.index') }}"
                  class="mb-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">

                {{-- ====== Tabs (Flowbite) ====== --}}
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400"
                    id="filterTabs" data-tabs-toggle="#filterTabsContent" role="tablist">
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-3 border-b-2 rounded-t-lg aria-selected:border-blue-600"
                                id="tab-personal-btn" data-tabs-target="#tab-personal" type="button" role="tab"
                                aria-controls="tab-personal" aria-selected="true">
                            Información personal
                        </button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-3 border-b-2 rounded-t-lg"
                                id="tab-alertas-btn" data-tabs-target="#tab-alertas" type="button" role="tab"
                                aria-controls="tab-alertas" aria-selected="false">
                            Alertas
                        </button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-3 border-b-2 rounded-t-lg"
                                id="tab-traslado-btn" data-tabs-target="#tab-traslado" type="button" role="tab"
                                aria-controls="tab-traslado" aria-selected="false">
                            Información traslado
                        </button>
                    </li>
                    <li role="presentation">
                        <button class="inline-block p-3 border-b-2 rounded-t-lg"
                                id="tab-grados-btn" data-tabs-target="#tab-grados" type="button" role="tab"
                                aria-controls="tab-grados" aria-selected="false">
                            Custom grados
                        </button>
                    </li>
                </ul>

                <div id="filterTabsContent">
                    {{-- =================== TAB: INFORMACIÓN PERSONAL =================== --}}
                    <div class="p-4 rounded-lg" id="tab-personal" role="tabpanel" aria-labelledby="tab-personal-btn">
                        <div class="grid grid-cols-12 gap-3 text-xs">

                            {{-- Básicos--}}
                            <div class="col-span-12 md:col-span-3">
                                <label for="cedula" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cédula</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                                        <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM4 21a8 8 0 1116 0H4z"/>
                                        </svg>
                                    </div>
                                    <input id="cedula" type="text" name="cedula" value="{{ request('cedula') }}"
                                           placeholder="Ej. 0102030405"
                                           class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 ps-10 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400" />
                                </div>
                            </div>

                            <div class="col-span-12 md:col-span-3">
                                <label for="apellidos_nombres" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Apellidos y Nombres</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                                        <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <input id="apellidos_nombres" type="text" name="apellidos_nombres" value="{{ request('apellidos_nombres') }}"
                                           placeholder="Ej. PÉREZ LÓPEZ JUAN"
                                           class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 ps-10 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400" />
                                </div>
                            </div>

                            <div class="col-span-6 md:col-span-2">
                                <label for="grado" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Grado</label>
                                <select id="grado" name="grado"
                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option value="">— Todos —</option>
                                    @foreach($gradosOrdenados as $g)
                                        <option value="{{ $g }}" @selected(request('grado')===$g)>{{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 md:col-span-2">
                                <label for="sexo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sexo</label>
                                <select id="sexo" name="sexo"
                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option value="">— Todos —</option>
                                    @foreach($sexos as $s)
                                        <option value="{{ $s }}" @selected(request('sexo')===$s)>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 md:col-span-2">
                                <label for="tipo_personal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo Personal</label>
                                <select id="tipo_personal" name="tipo_personal"
                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-600 focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option value="">— Todos —</option>
                                    @foreach($tiposPersonal as $tp)
                                        <option value="{{ $tp }}" @selected(request('tipo_personal')===$tp)>{{ $tp }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Promoción (múltiple) --}}
                            <div class="col-span-12 md:col-span-6">
                                <label for="prom-q" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Promoción (múltiple)
                                </label>

                                <input
                                    type="text"
                                    id="prom-q"
                                    placeholder="Buscar promoción…"
                                    class="block w-full mb-2 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 placeholder-gray-400
                                       focus:ring-primary-600 focus:border-primary-600
                                       dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                >

                                <div
                                    id="prom-list"
                                    class="h-44 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1"
                                >
                                    @foreach($promociones as $p)
                                        <label class="flex items-center gap-2 text-xs text-gray-900 dark:text-gray-100">
                                            <input
                                                type="checkbox"
                                                class="prom-item w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                                name="promocion[]"
                                                value="{{ $p }}"
                                                @checked(in_array($p,$promSel))
                                            >
                                            <span>{{ $p }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <button type="button" id="prom-all"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100
                                            dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-800">
                                        Seleccionar todo
                                    </button>
                                    <button type="button" id="prom-filtered"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100
                                            dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-800">
                                        Seleccionar filtrados
                                    </button>
                                    <button type="button" id="prom-clear"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100
                                            dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 dark:hover:bg-gray-600">
                                        Limpiar
                                    </button>
                                </div>
                            </div>

                            {{-- Provincia (trabaja) – múltiple --}}
                            <div class="col-span-12 md:col-span-6">
                                <label for="prov-q" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Provincia (trabaja) – múltiple
                                </label>

                                <input
                                    type="text"
                                    id="prov-q"
                                    placeholder="Buscar provincia…"
                                    class="block w-full mb-2 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 placeholder-gray-400
                                   focus:ring-primary-600 focus:border-primary-600
                                   dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                >

                                <div
                                    id="prov-list"
                                    class="h-44 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1"
                                >
                                    @foreach($provinciasTrab as $pv)
                                        <label class="flex items-center gap-2 text-xs text-gray-900 dark:text-gray-100">
                                            <input
                                                type="checkbox"
                                                class="prov-item w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                                name="provincia_trabaja[]"
                                                value="{{ $pv }}"
                                                @checked(in_array($pv,$provTrabSel))
                                            >
                                            <span>{{ $pv }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <button type="button" id="prov-all"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100
                                            dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-800">
                                        Seleccionar todo
                                    </button>
                                    <button type="button" id="prov-filtered"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100
                                            dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-800">
                                        Seleccionar filtrados
                                    </button>
                                    <button type="button" id="prov-clear"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100
                                            dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 dark:hover:bg-gray-600">
                                        Limpiar
                                    </button>
                                </div>
                            </div>


                        </div>
                    </div>

                    {{-- =================== TAB: ALERTAS =================== --}}
                    <div class="hidden p-4 rounded-lg" id="tab-alertas" role="tabpanel" aria-labelledby="tab-alertas-btn">
                        <div class="grid grid-cols-12 gap-3 text-xs">

                            {{-- Alertas (múltiple; excluye coincidencias) --}}
                            <div class="col-span-12">
                                <label for="alert-q" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Alertas (múltiple)
                                </label>

                                <input
                                    type="text"
                                    id="alert-q"
                                    placeholder="Buscar alerta…"
                                    class="block w-full mb-2 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 placeholder-gray-400
                                       focus:ring-primary-600 focus:border-primary-600
                                       dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                >

                                <div
                                    id="alert-list"
                                    class="h-44 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1"
                                >
                                    @foreach($alertCatalog as $a)
                                        <label class="flex items-center gap-2 text-xs text-gray-900 dark:text-gray-100">
                                            <input
                                                type="checkbox"
                                                class="alert-item w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                                name="alertas_sel_key[]"
                                                value="{{ $a['key'] }}"
                                                @checked(in_array($a['key'], $alertsSelKeys))
                                            >
                                            <span>{{ $a['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <p class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                                    Excluye coincidencias en <code>alertas</code>, <code>alerta_devengacion</code> o <code>alerta_marco_legal</code>.
                                </p>

                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <button type="button" id="alert-all"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100
                                            dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-800">
                                        Seleccionar todo
                                    </button>
                                    <button type="button" id="alert-filtered"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100
                                            dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-800">
                                        Seleccionar filtrados
                                    </button>
                                    <button type="button" id="alert-clear"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100
                                            dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 dark:hover:bg-gray-600">
                                        Limpiar
                                    </button>
                                </div>
                            </div>


                            {{-- =================== Sección: Banderas a excluir + Fase Maternidad/Lactancia =================== --}}
                            {{-- Col: 1/1 en móvil, 1/2 en md+ --}}
                            <div class="col-span-12">
                                {{-- Banderas a excluir --}}
                                @php
                                    $FLAGS = [
                                      'contrato_estudios'          => 'Contrato estudios',
                                      'conyuge_policia_activo'     => 'Cónyuge policía activo',
                                      'enf_catast_sp'              => 'Enf. catastrófica SP',
                                      'enf_catast_conyuge_hijos'   => 'Enf. catast. C/H',
                                      'discapacidad_sp'            => 'Discapacidad SP',
                                      'discapacidad_conyuge_hijos' => 'Discapacidad C/H',
                                      'novedad_situacion'          => 'Novedad situación',
                                      'observacion_tenencia'       => 'Observación tenencia',
                                      'alertas_problemas_salud'    => 'Alerta problemas de salud',
                                      'fase_maternidad'            => 'Fase Maternidad 1',
                                      'FaseMaternidadUDGA'         => 'Fase Maternidad 2',
                                    ];
                                @endphp

                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Banderas a excluir</label>

                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                                    <div class="flex items-center justify-between">
                                        <label class="inline-flex items-center gap-2 text-xs text-gray-900 dark:text-gray-100">
                                            <input type="checkbox" id="flags-all"
                                                   class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                            <span class="font-medium">Seleccionar todo</span>
                                        </label>
                                        <button type="button" id="flags-clear"
                                                class="text-[11px] px-2 py-1 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100
                                                dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">
                                            Limpiar
                                        </button>
                                    </div>

                                    <div id="flags-container" class="mt-3 flex flex-wrap items-center gap-3">
                                        @foreach($FLAGS as $key => $label)
                                            <label class="inline-flex items-center gap-2 text-xs text-gray-900 dark:text-gray-100">
                                                <input type="checkbox" name="flags[]" value="{{ $key }}" @checked(in_array($key,$flagsSel))
                                                class="flag-item w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                                <span>{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    <p class="mt-3 text-[11px] text-gray-500 dark:text-gray-400">
                                        Se excluirán los usuarios que tengan <strong>cualquiera</strong> de las banderas seleccionadas.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- =================== TAB: INFORMACIÓN TRASLADO (Flowbite styles) =================== --}}
                    <div class="hidden p-4 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700" id="tab-traslado" role="tabpanel" aria-labelledby="tab-traslado-btn">
                        <div class="grid grid-cols-12 gap-4 text-xs">

                            {{-- Nomenclatura efectiva --}}
                            <div class="col-span-12 md:col-span-6">
                                <label for="nom-q" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomenclatura efectiva (múltiple)</label>
                                <input
                                    type="text"
                                    id="nom-q"
                                    placeholder="Buscar nomenclatura…"
                                    class="block w-full mb-2 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 placeholder-gray-400
                                   focus:ring-primary-600 focus:border-primary-600
                                   dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                >
                                <div
                                    id="nom-list"
                                    class="h-44 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1"
                                >
                                    @foreach($nomenclaturas as $opt)
                                        <label class="flex items-center gap-2 text-xs text-gray-900 dark:text-gray-100">
                                            <input
                                                type="checkbox"
                                                class="nom-item w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                                name="nomenclatura_efectiva[]"
                                                value="{{ $opt }}"
                                                @checked(in_array($opt, $nomenclaturaSel))
                                            >
                                            <span>{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <button type="button" id="nom-all"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100
                                            dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-800">
                                        Seleccionar todo
                                    </button>
                                    <button type="button" id="nom-filtered"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100
                                            dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-800">
                                        Seleccionar filtrados
                                    </button>
                                    <button type="button" id="nom-clear"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100
                                            dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 dark:hover:bg-gray-600">
                                        Limpiar
                                    </button>
                                </div>
                            </div>

                            {{-- Función efectiva --}}
                            <div class="col-span-12 md:col-span-6">
                                <label for="fun-q" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Función efectiva (múltiple)</label>
                                <input
                                    type="text"
                                    id="fun-q"
                                    placeholder="Buscar función…"
                                    class="block w-full mb-2 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 placeholder-gray-400
                                       focus:ring-primary-600 focus:border-primary-600
                                       dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                >
                                <div
                                    id="fun-list"
                                    class="h-44 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1"
                                >
                                    @foreach($funciones as $opt)
                                        <label class="flex items-center gap-2 text-xs text-gray-900 dark:text-gray-100">
                                            <input
                                                type="checkbox"
                                                class="fun-item w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                                name="funcion_efectiva[]"
                                                value="{{ $opt }}"
                                                @checked(in_array($opt, $funcionSel))
                                            >
                                            <span>{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <button type="button" id="fun-all"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100
                                            dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-800">
                                        Seleccionar todo
                                    </button>
                                    <button type="button" id="fun-filtered"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100
                                            dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-800">
                                        Seleccionar filtrados
                                    </button>
                                    <button type="button" id="fun-clear"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100
                                            dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 dark:hover:bg-gray-600">
                                        Limpiar
                                    </button>
                                </div>
                            </div>

                            {{-- Estado efectivo --}}
                            <div class="col-span-12 md:col-span-6">
                                <label for="est-q" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado efectivo (múltiple)</label>
                                <input
                                    type="text"
                                    id="est-q"
                                    placeholder="Buscar estado…"
                                    class="block w-full mb-2 rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 placeholder-gray-400
                                       focus:ring-primary-600 focus:border-primary-600
                                       dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                >
                                <div
                                    id="est-list"
                                    class="h-44 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1"
                                >
                                    @foreach($estadosEfectivos as $opt)
                                        <label class="flex items-center gap-2 text-xs text-gray-900 dark:text-gray-100">
                                            <input
                                                type="checkbox"
                                                class="est-item w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                                name="estado_efectivo[]"
                                                value="{{ $opt }}"
                                                @checked(in_array($opt, $estadoSel))
                                            >
                                            <span>{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <button type="button" id="est-all"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100
                                            dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-800">
                                        Seleccionar todo
                                    </button>
                                    <button type="button" id="est-filtered"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-emerald-100
                                            dark:bg-emerald-900/30 dark:text-emerald-200 dark:border-emerald-800">
                                        Seleccionar filtrados
                                    </button>
                                    <button type="button" id="est-clear"
                                            class="text-[11px] px-2.5 py-1 rounded-lg bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100
                                            dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 dark:hover:bg-gray-600">
                                        Limpiar
                                    </button>
                                </div>
                            </div>

                            {{-- Fecha efectiva (buckets) --}}
                            <div class="col-span-12 md:col-span-6">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha efectiva (múltiple)</label>
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3 space-y-2">
                                    @php $FECHA = ['lt1'=>'Menos de 1 año','gte1'=>'Más de 1 año','gte2'=>'Más de 2 años','gte5'=>'Más de 5 años']; @endphp
                                    @foreach($FECHA as $k=>$txt)
                                        <label class="inline-flex items-center gap-2 mr-4 text-xs text-gray-900 dark:text-gray-100">
                                            <input
                                                type="checkbox"
                                                name="fecha_efectiva_bucket[]"
                                                value="{{ $k }}"
                                                @checked(in_array($k, $fechaSel))
                                                class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                            >
                                            <span>{{ $txt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>


                    {{-- =================== TAB: CUSTOM GRADOS =================== --}}
                    <div class="hidden p-4 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700" id="tab-grados" role="tabpanel" aria-labelledby="tab-grados-btn">
                        <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10.75 2a.75.75 0 0 1 .75.75V5h2.25a.75.75 0 0 1 0 1.5H11.5v2.25a.75.75 0 0 1-1.5 0V6.5H7.75a.75.75 0 0 1 0-1.5H10V2.75a.75.75 0 0 1 .75-.75Z"/>
                                <path d="M4 5a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V5Zm3-1.5A1.5 1.5 0 0 0 5.5 5v10A1.5 1.5 0 0 0 7 16.5h6a1.5 1.5 0 0 0 1.5-1.5V5A1.5 1.5 0 0 0 13 3.5H7Z"/>
                            </svg>
                            Requerimientos por grado
                        </h3>

                        @php
                            // Orden jerárquico: GENERAL → POLICÍA
                            $ordenJerarquico = [
                                'GRAS','GRAI','GRAD',   // Generales
                                'CRNL','TCNL','MAYR',   // Superiores
                                'CPTN','TNTE','SBTE',   // Subalternos oficiales
                                'SBOM','SBOP','SBOS',   // Suboficiales mayores/sargentos mayores
                                'SGOP','SGOS','CBOP','CBOS', // Sargentos/Cabos
                                'POLI'                  // Policía
                            ];
                            $rank = array_flip($ordenJerarquico);
                            $gradosOrdenadosUI = collect($gradosOrdenados)
                                ->sortBy(fn($x) => $rank[$x] ?? 999) // Los no mapeados se van al final
                                ->values();
                        @endphp

                        <div class="grid grid-cols-12 gap-3">
                            @foreach($gradosOrdenadosUI as $g)
                                <div class="col-span-6 md:col-span-2">
                                    <label class="block mb-1.5 text-xs font-medium text-gray-900 dark:text-gray-200">{{ $g }}</label>
                                    <input
                                        type="number"
                                        min="0"
                                        name="req_grados[{{ $g }}]"
                                        value="{{ (int) data_get(request()->query('req_grados',[]), $g, 0) }}"
                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2 text-sm text-gray-900 placeholder-gray-400
                                           focus:ring-primary-600 focus:border-primary-600
                                           dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                        placeholder="0"
                                    >
                                </div>
                            @endforeach
                        </div>

                        {{-- Nota opcional: puedes quitar este texto si no lo necesitas --}}
                        <p class="mt-3 text-[11px] text-gray-500 dark:text-gray-400">
                            * Los grados no reconocidos en la jerarquía se muestran al final.
                        </p>
                    </div>


                </div>

                {{-- Acciones globales --}}
                <div class="mt-3 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-xs hover:bg-blue-700">Aplicar filtros</button>
                    <a href="{{ route('generar_pases.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md text-xs">Limpiar</a>
                    <button type="submit" name="export" value="excel"
                            class="px-4 py-2 bg-emerald-600 text-white rounded-md text-xs hover:bg-emerald-700">
                        Exportar base
                    </button>
                </div>

            </form>

            {{-- ====== Resultados ====== --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100">
                    <tr>
                        <th class="px-2 py-2 text-left">Cédula</th>
                        <th class="px-2 py-2 text-left">Apellidos Nombres</th>
                        <th class="px-2 py-2 text-left">Grado</th>
                        <th class="px-2 py-2 text-left">Sexo</th>
                        <th class="px-2 py-2 text-left">Tipo</th>
                        <th class="px-2 py-2 text-left">Fecha efectiva</th>
                        <th class="px-2 py-2 text-left">Nomenclatura efectiva</th>
                        <th class="px-2 py-2 text-left">Función efectiva</th>
                        <th class="px-2 py-2 text-left">Estado efectivo</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-800 dark:text-gray-100">
                    @forelse($usuarios as $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-2 py-2">{{ $u->cedula }}</td>
                            <td class="px-2 py-2">{{ $u->apellidos_nombres }}</td>
                            <td class="px-2 py-2">{{ $u->grado }}</td>
                            <td class="px-2 py-2">{{ $u->sexo }}</td>
                            <td class="px-2 py-2">{{ $u->tipo_personal }}</td>
                            <td class="px-2 py-2">{{ $u->fecha_efectiva }}</td>
                            <td class="px-2 py-2">{{ $u->nomenclatura_efectiva }}</td>
                            <td class="px-2 py-2">{{ $u->funcion_efectiva }}</td>
                            <td class="px-2 py-2">{{ $u->estado_efectivo }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-2 py-6 text-center text-gray-500 dark:text-gray-300">No hay resultados</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="px-3 py-2">
                    {{ $usuarios->links() }}
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function initMulti(prefix) {
            const q   = document.getElementById(prefix + '-q');
            const box = document.getElementById(prefix + '-list');
            const all = document.getElementById(prefix + '-all');
            const fil = document.getElementById(prefix + '-filtered');
            const clr = document.getElementById(prefix + '-clear');
            if (!box) return;

            function labels(){ return box.querySelectorAll('label'); }
            function cbOf(label){ return label.querySelector('input[type=checkbox]'); }

            q && q.addEventListener('input', function(){
                const term = this.value.trim().toLowerCase();
                labels().forEach(l => {
                    const text = l.textContent.trim().toLowerCase();
                    l.style.display = text.includes(term) ? '' : 'none';
                });
            });

            all && all.addEventListener('click', () => {
                box.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = true);
            });

            fil && fil.addEventListener('click', () => {
                labels().forEach(l => { if (l.style.display !== 'none') cbOf(l).checked = true; });
            });

            clr && clr.addEventListener('click', () => {
                box.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
                if (q) { q.value=''; labels().forEach(l => l.style.display=''); }
            });
        }

        /* Inicializar multiselects */
        ['prom','prov','alert','nom','fun','est','fase-ml'].forEach(initMulti);

        /* Banderas: seleccionar todo / limpiar */
        (function(){
            const master = document.getElementById('flags-all');
            const clear  = document.getElementById('flags-clear');
            const cont   = document.getElementById('flags-container');
            if (!cont) return;

            function items(){ return cont.querySelectorAll('.flag-item'); }

            master && master.addEventListener('change', function(){
                items().forEach(cb => cb.checked = master.checked);
            });

            clear && clear.addEventListener('click', function(){
                items().forEach(cb => cb.checked = false);
                if (master) master.checked = false;
            });
        })();
    </script>
@endpush
