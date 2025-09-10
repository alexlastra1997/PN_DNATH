@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 py-0">
        <div class="mx-auto max-w-screen-2xl px-0 lg:px-0">
            <div class="bg-white dark:bg-gray-800 relative rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">

                {{-- HEADER --}}
                <div class="border-b dark:border-gray-700">
                    <div class="flex items-center justify-between pt-3 px-3">
                        <h5 class="dark:text-white font-semibold">Resultados de usuarios</h5>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('usuarios.opciones') }}"
                               class="py-2 px-3 text-sm font-medium text-white bg-gray-900/90 rounded-md hover:bg-black">Cargar Excel</a>
                            <a href="{{ route('usuarios.carrito') }}"
                               class="py-2 px-3 text-sm font-medium text-white bg-primary-700 rounded-md hover:bg-primary-800">Ver carrito</a>
                            <a href="{{ route('usuarios.resultados', ['clear' => 1]) }}"
                               class="py-2 px-3 text-sm font-medium text-gray-900 bg-white rounded-md border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white">
                                Limpiar universo
                            </a>
                        </div>
                    </div>

                    {{-- FILTROS --}}
                    <form id="filtros-form" method="GET" action="{{ route('usuarios.resultados') }}" class="py-3 px-3">
                        @if(!empty($cedulas))
                            <input type="hidden" name="cedulas" value="{{ implode(',', $cedulas) }}">
                        @endif

                        <div class="grid grid-cols-12 gap-3 items-end">
                            {{-- Buscar --}}
                            <div class="col-span-12 md:col-span-4">
                                <label for="q" class="sr-only">Buscar</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <input type="search" id="q" name="q" value="{{ $q }}"
                                           class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-l-md bg-gray-50 focus:ring-0 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                           placeholder="Ej: 1717..., Juan, TNTE, Pichincha">
                                    <button type="submit"
                                            class="text-white absolute right-0 inset-y-0 bg-primary-700 hover:bg-primary-800 focus:ring-0 focus:outline-none font-medium rounded-r-md text-sm px-4 dark:bg-primary-600 dark:hover:bg-primary-700">
                                        Buscar
                                    </button>
                                </div>
                            </div>

                            {{-- Alertas (múltiple) --}}
                            <div class="col-span-12 md:col-span-6">
                                <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-200">Alertas (múltiple)</label>
                                <select id="filtro-alertas" name="alertas[]" multiple size="8"
                                        class="w-full h-40 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none">
                                    @foreach($opcionesAlertas as $opt)
                                        <option value="{{ $opt }}" {{ in_array($opt, $alertasSeleccionadas, true) ? 'selected' : '' }}>
                                            {{ $opt }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-gray-500 mt-1">Ctrl/Cmd o Shift para selección múltiple.</p>
                            </div>

                            {{-- Estado --}}
                            <div class="col-span-12 md:col-span-2">
                                <label for="filtro-estado" class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-200">Mostrar</label>
                                <select id="filtro-estado" name="estado"
                                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none">
                                    <option value="todos"  {{ $estadoSeleccionado==='todos'  ? 'selected' : '' }}>Todos</option>
                                    <option value="alerta" {{ $estadoSeleccionado==='alerta' ? 'selected' : '' }}>Solo con ALERTA</option>
                                    <option value="activo" {{ $estadoSeleccionado==='activo' ? 'selected' : '' }}>Solo Activos</option>
                                </select>
                            </div>

                            {{-- Botones --}}
                            <div class="col-span-12 flex items-center justify-end gap-2">
                                <button type="submit"
                                        class="px-4 py-2 rounded-md text-white bg-primary-700 hover:bg-primary-800 focus:ring-0 focus:outline-none text-sm dark:bg-primary-600 dark:hover:bg-primary-700">
                                    Aplicar
                                </button>
                                <a href="{{ route('usuarios.resultados', ['clear' => 1]) }}"
                                   class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-sm text-center text-gray-800 dark:text-gray-200">
                                    Limpiar filtros
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- TABLA + SEMÁFORO + 3 PUNTOS + MODAL VER --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="p-3 w-10">
                                <input id="checkbox-all" type="checkbox"
                                       class="w-4 h-4 text-primary-600 bg-gray-100 rounded border-gray-300 focus:ring-0 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                            </th>
                            <th class="px-3 py-3">CÉDULA</th>
                            <th class="px-3 py-3">APELLIDOS NOMBRES</th>
                            <th class="px-3 py-3">GRADO</th>
                            <th class="px-3 py-3">SEMÁFORO</th>
                            <th class="px-3 py-3"><span class="sr-only">Acciones</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            // Etiquetas legibles
                            $labels = [
                              'cedula' => 'Cédula',
                              'apellidos_nombres' => 'Apellidos y Nombres',
                              'grado' => 'Grado',
                              'provincia_trabaja' => 'Provincia trabaja',
                              'estado_civil' => 'Estado civil',
                              'promocion' => 'Promoción',
                              'contrato_estudios' => 'Contrato de Estudios',
                              'nivel_educativo' => 'Nivel educativo',
                              'titulo' => 'Título','especialidad'=>'Especialidad','institucion'=>'Institución','anio_graduacion'=>'Año de graduación',
                              'alertas' => 'Alertas','alerta_devengacion'=>'Alerta Devengación','alerta_marco_legal'=>'Alerta Marco Legal',
                              'novedad_situacion'=>'Novedad Situación','observacion_tenencia'=>'Observación Tenencia','pase_ucp_ccp_cpl'=>'Pase UCP/CCP/CPL',
                              'alertas_problemas_salud'=>'Alertas Problemas de Salud','FaseMaternidadUDGA'=>'Fase Maternidad UDGA','fase_maternidad'=>'Fase Maternidad','maternidad'=>'Maternidad',
                              'enf_catast_sp'=>'Enfermedad Catastrófica SP','enf_catast_conyuge_hijos'=>'Enf. Catastrófica Cónyuge/Hijos','discapacidad_sp'=>'Discapacidad SP','discapacidad_conyuge_hijos'=>'Discapacidad Cónyuge/Hijos',
                            ];
                            $colsAlerta = ['alertas','alerta_devengacion','alerta_marco_legal'];
                            $colsOtros  = ['novedad_situacion','observacion_tenencia','contrato_estudios','pase_ucp_ccp_cpl'];
                            $colsSalud  = ['alertas_problemas_salud','FaseMaternidadUDGA','fase_maternidad','maternidad','enf_catast_sp','enf_catast_conyuge_hijos','discapacidad_sp','discapacidad_conyuge_hijos'];

                            $personalFields  = ['cedula','apellidos_nombres','grado','provincia_trabaja','estado_civil','promocion'];
                            $educacionFields = ['contrato_estudios','nivel_educativo','titulo','especialidad','institucion','anio_graduacion'];

                            $hasVal = function($v) {
                              if ($v === null) return false; $s = strtoupper(trim((string)$v));
                              return $s !== '' && !in_array($s, ['NO','N/A','NA','0'], true);
                            };

                            $carApto   = session('carrito.apto', []);
                            $carNoApto = session('carrito.no_apto', []);
                        @endphp

                        @forelse ($usuarios as $u)
                            @php
                                $hitA = []; foreach($colsAlerta as $c){ if($hasVal($u->{$c} ?? null)) $hitA[$c] = $u->{$c}; }
                                $hitO = []; foreach($colsOtros  as $c){ if($hasVal($u->{$c} ?? null)) $hitO[$c] = $u->{$c}; }
                                $tmpFaseUDGA = $u->{'FaseMaternidadUDGA'} ?? null;
                                $hitS = [];
                                foreach($colsSalud as $c){
                                  $val = ($c === 'FaseMaternidadUDGA') ? $tmpFaseUDGA : ($u->{$c} ?? null);
                                  if($hasVal($val)) $hitS[$c] = $val;
                                }
                                $categoria = 'SIN ALERTA'; $colorDot = 'bg-green-500'; $tieneAlgo=false;
                                if(count($hitA)){ $categoria='ALERTA'; $colorDot='bg-red-500'; $tieneAlgo=true; }
                                elseif(count($hitO)){ $categoria='OTROS'; $colorDot='bg-yellow-400'; $tieneAlgo=true; }
                                elseif(count($hitS)){ $categoria='SALUD'; $colorDot='bg-sky-400'; $tieneAlgo=true; }

                                $keyCed = preg_replace('/\D+/', '', (string)$u->cedula);
                                if (strlen($keyCed) > 10) { $keyCed = substr($keyCed, -10); }
                                $keyCed = str_pad($keyCed, 10, '0', STR_PAD_LEFT);

                                $yaApto   = isset($carApto[$keyCed]);
                                $yaNoApto = isset($carNoApto[$keyCed]);

                                $modalSemId = 'modal-' . preg_replace('/\W+/', '', (string)$u->cedula);
                                $viewId     = 'view-'  . preg_replace('/\W+/', '', (string)$u->cedula);
                            @endphp

                            <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700" data-cedula="{{ $u->cedula }}">
                                <td class="px-3 py-3">
                                    <input type="checkbox" onclick="event.stopPropagation()"
                                           class="row-check w-4 h-4 text-primary-600 bg-gray-100 rounded border-gray-300 focus:ring-0 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                                </td>
                                <td class="px-3 py-3 font-mono">{{ $u->cedula }}</td>

                                {{-- NOMBRES + INDICADOR --}}
                                <td class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <span>{{ $u->apellidos_nombres }}</span>
                                    @if($yaApto)
                                        <span class="mark-calif ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-700" title="Ya seleccionado: APTO">✓ APTO</span>
                                    @elseif($yaNoApto)
                                        <span class="mark-calif ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-700" title="Ya seleccionado: NO APTO">✕ NO APTO</span>
                                    @else
                                        <span class="mark-calif ml-2"></span>
                                    @endif
                                </td>

                                <td class="px-3 py-3">{{ $u->grado }}</td>

                                {{-- SEMÁFORO --}}
                                <td class="px-3 py-3">
                                    @if($tieneAlgo)
                                        <button type="button" data-modal-open="{{ $modalSemId }}"
                                                class="inline-flex items-center gap-2 px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none">
                                            <span class="inline-block w-3 h-3 rounded-full {{ $colorDot }}"></span>
                                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $categoria }}</span>
                                        </button>

                                        {{-- MODAL DETALLE SEMÁFORO --}}
                                        <div id="{{ $modalSemId }}" class="fixed inset-0 z-40 hidden">
                                            <div data-modal-close="{{ $modalSemId }}" class="absolute inset-0 bg-black/40"></div>
                                            <div class="relative z-50 mx-auto mt-24 w-[92%] max-w-lg rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl">
                                                <div class="flex items-center justify-between px-4 py-3 border-b dark:border-gray-700">
                                                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                        Detalle de {{ $categoria }} — {{ $u->apellidos_nombres }} ({{ $u->cedula }})
                                                    </h3>
                                                    <button type="button" data-modal-close="{{ $modalSemId }}" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">✕</button>
                                                </div>
                                                <div class="p-4 space-y-4 text-sm">
                                                    @if(count($hitA))
                                                        <div>
                                                            <div class="font-semibold text-red-600 dark:text-red-400 mb-1">ALERTA</div>
                                                            <ul class="list-disc pl-5 space-y-1">
                                                                @foreach($hitA as $k=>$v) <li><span class="font-medium">{{ $labels[$k] ?? $k }}:</span> {{ $v }}</li> @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    @if(count($hitO))
                                                        <div>
                                                            <div class="font-semibold text-yellow-600 dark:text-yellow-400 mb-1">OTROS</div>
                                                            <ul class="list-disc pl-5 space-y-1">
                                                                @foreach($hitO as $k=>$v) <li><span class="font-medium">{{ $labels[$k] ?? $k }}:</span> {{ $v }}</li> @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    @if(count($hitS))
                                                        <div>
                                                            <div class="font-semibold text-sky-600 dark:text-sky-400 mb-1">SALUD</div>
                                                            <ul class="list-disc pl-5 space-y-1">
                                                                @foreach($hitS as $k=>$v) <li><span class="font-medium">{{ $labels[$k] ?? $k }}:</span> {{ $v }}</li> @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="px-4 py-3 border-t dark:border-gray-700 text-right">
                                                    <button type="button" data-modal-close="{{ $modalSemId }}"
                                                            class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-sm text-gray-800 dark:text-gray-200">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700">
                      <span class="inline-block w-3 h-3 rounded-full bg-green-500"></span>
                      <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">SIN ALERTA</span>
                    </span>
                                    @endif
                                </td>

                                {{-- ACCIONES --}}
                                <td class="px-3 py-3 relative">
                                    <button type="button"
                                            class="inline-flex items-center p-1 text-sm text-gray-500 hover:text-gray-800 hover:bg-gray-200 rounded-md focus:outline-none dark:text-gray-400 dark:hover:text-gray-100 dark:hover:bg-gray-700"
                                            data-menu-toggle="menu-{{ $u->cedula }}">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg>
                                    </button>
                                    <div id="menu-{{ $u->cedula }}" class="hidden absolute right-0 z-20 mt-2 w-44 bg-white rounded-md shadow-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                                        <ul class="py-1 text-sm">
                                            <li>
                                                <a href="#"
                                                   data-action="ver"
                                                   data-cedula="{{ $u->cedula }}"
                                                   data-view-id="{{ $viewId }}"
                                                   class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200">VER</a>
                                            </li>
                                            <li><a href="#" data-action="apto"    data-cedula="{{ $u->cedula }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200">APTO</a></li>
                                            <li><a href="#" data-action="no_apto" data-cedula="{{ $u->cedula }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200">NO APTO</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            {{-- MODAL VER (FICHA) --}}
                            <div id="{{ $viewId }}" class="fixed inset-0 z-50 hidden">
                                <div data-modal-close="{{ $viewId }}" class="absolute inset-0 bg-black/40"></div>
                                <div class="relative z-50 mx-auto mt-16 w-[95%] max-w-4xl rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                                    <div class="flex items-start justify-between px-5 py-4 border-b dark:border-gray-700">
                                        <div>
                                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Ficha del servidor</h3>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $u->apellidos_nombres }} — {{ $u->cedula }}</p>
                                        </div>
                                        <button type="button" data-modal-close="{{ $viewId }}" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">✕</button>
                                    </div>

                                    <div class="px-5 py-5 space-y-6 text-sm">
                                        {{-- Información personal --}}
                                        <section>
                                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Información personal</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                @foreach($personalFields as $f)
                                                    @php $val = $u->{$f} ?? null; @endphp
                                                    @if($hasVal($val))
                                                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md px-3 py-2">
                                                            <div class="text-[11px] text-gray-500 dark:text-gray-400">{{ $labels[$f] ?? $f }}</div>
                                                            <div class="font-medium text-gray-800 dark:text-gray-100">{{ $val }}</div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </section>

                                        {{-- Educación --}}
                                        <section>
                                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-2">Educación</h4>
                                            @php $eduShown = false; @endphp
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                @foreach($educacionFields as $f)
                                                    @php $val = ($f==='FaseMaternidadUDGA') ? ($u->{'FaseMaternidadUDGA'} ?? null) : ($u->{$f} ?? null); @endphp
                                                    @if($hasVal($val)) @php $eduShown = true; @endphp
                                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md px-3 py-2">
                                                        <div class="text-[11px] text-gray-500 dark:text-gray-400">{{ $labels[$f] ?? $f }}</div>
                                                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $val }}</div>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            @if(!$eduShown)
                                                <div class="text-gray-500 dark:text-gray-400 text-xs">Sin información de educación.</div>
                                            @endif
                                        </section>

                                        {{-- Alertas --}}
                                        <section>
                                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-3">Alertas</h4>
                                            <div class="space-y-4">
                                                @if(count($hitA))
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-2"><span class="inline-block w-2 h-2 rounded-full bg-red-500"></span><span class="text-xs font-semibold text-red-600 dark:text-red-400">ALERTA</span></div>
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            @foreach($hitA as $k=>$v)
                                                                <div class="bg-red-50 dark:bg-red-900/20 rounded-md px-3 py-2">
                                                                    <div class="text-[11px] text-red-700 dark:text-red-300">{{ $labels[$k] ?? $k }}</div>
                                                                    <div class="font-medium text-red-800 dark:text-red-200">{{ $v }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                @if(count($hitO))
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-2"><span class="inline-block w-2 h-2 rounded-full bg-yellow-400"></span><span class="text-xs font-semibold text-yellow-700 dark:text-yellow-300">OTROS</span></div>
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            @foreach($hitO as $k=>$v)
                                                                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-md px-3 py-2">
                                                                    <div class="text-[11px] text-yellow-700 dark:text-yellow-300">{{ $labels[$k] ?? $k }}</div>
                                                                    <div class="font-medium text-yellow-800 dark:text-yellow-200">{{ $v }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                @if(count($hitS))
                                                    <div>
                                                        <div class="flex items-center gap-2 mb-2"><span class="inline-block w-2 h-2 rounded-full bg-sky-400"></span><span class="text-xs font-semibold text-sky-700 dark:text-sky-300">SALUD</span></div>
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            @foreach($hitS as $k=>$v)
                                                                <div class="bg-sky-50 dark:bg-sky-900/20 rounded-md px-3 py-2">
                                                                    <div class="text-[11px] text-sky-700 dark:text-sky-300">{{ $labels[$k] ?? $k }}</div>
                                                                    <div class="font-medium text-sky-800 dark:text-sky-200">{{ $v }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                @if(!count($hitA) && !count($hitO) && !count($hitS))
                                                    <div class="text-gray-500 dark:text-gray-400 text-xs">Sin alertas registradas.</div>
                                                @endif
                                            </div>
                                        </section>
                                    </div>

                                    <div class="px-5 py-4 border-t dark:border-gray-700 text-right">
                                        <button type="button" data-modal-close="{{ $viewId }}"
                                                class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-sm text-gray-800 dark:text-gray-200">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                            {{-- /MODAL VER --}}
                        @empty
                            <tr><td colspan="6" class="px-3 py-6 text-center text-gray-600 dark:text-gray-300">No hay resultados con los filtros actuales.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- FOOTER / PAGINACIÓN --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center pt-3 pb-4 px-3" aria-label="Table navigation">
                    <div class="text-xs">
                        <div class="text-gray-500 dark:text-gray-400 mb-1">Mostrando</div>
                        <div class="dark:text-white font-medium">
                            {{ $usuarios->firstItem() ?? 0 }}–{{ $usuarios->lastItem() ?? 0 }} de {{ $usuarios->total() }} resultados
                        </div>
                    </div>
                    <div>{{ $usuarios->links() }}</div>
                </div>

            </div>
        </div>
    </section>

    {{-- Modal Global de mensajes --}}
    <div id="alert-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/40" data-alert-close></div>
        <div class="relative z-10 mx-auto mt-28 w-[92%] max-w-md rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl">
            <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Aviso</h3>
                <button class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" data-alert-close>✕</button>
            </div>
            <div class="p-4 text-sm text-gray-800 dark:text-gray-100" id="alert-modal-text">Mensaje</div>
            <div class="px-4 py-3 border-t dark:border-gray-700 text-right">
                <button class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-sm text-gray-800 dark:text-gray-200" data-alert-close>Cerrar</button>
            </div>
        </div>
    </div>

    {{-- MODAL CONFIRMAR CALIFICACIÓN (NOVEDAD) --}}
    <div id="modal-calificar" class="fixed inset-0 z-50 hidden">
        <div data-modal-close="modal-calificar" class="absolute inset-0 bg-black/40"></div>
        <div class="relative z-50 mx-auto mt-24 w-[92%] max-w-lg rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
            <div class="px-5 py-3 border-b dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Confirmar calificación</h3>
                <button type="button" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" data-modal-close="modal-calificar">✕</button>
            </div>

            <form id="form-calificar" class="px-5 py-4 space-y-4">
                <input type="hidden" id="calif-cedula">
                <input type="hidden" id="calif-estado"> {{-- APTO | NO_APTO --}}

                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Novedad</label>
                    <select id="calif-novedad" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none">
                        <option value="SIN_NOVEDAD">Sin novedad</option>
                        <option value="NOVEDAD">Con novedad</option>
                    </select>
                </div>

                <div id="wrap-detalle" class="hidden">
                    <label for="calif-detalle" class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Detalle de la novedad</label>
                    <textarea id="calif-detalle" rows="4" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none" placeholder="Describe la novedad..."></textarea>
                    <p id="detalle-error" class="text-xs text-red-600 mt-1 hidden">Debes ingresar el detalle de la novedad.</p>
                </div>

                <div class="pt-2 text-right">
                    <button type="button" data-modal-close="modal-calificar" class="px-3 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-sm">Cancelar</button>
                    <button type="submit" class="ml-2 px-3 py-2 rounded-md bg-primary-700 hover:bg-primary-800 text-sm text-white">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // autosubmit + seleccionar todos
            const form    = document.getElementById('filtros-form');
            const estado  = document.getElementById('filtro-estado');
            const alertas = document.getElementById('filtro-alertas');
            const chkAll  = document.getElementById('checkbox-all');
            if (estado)  estado.addEventListener('change', () => form.submit());
            if (alertas) alertas.addEventListener('change', () => form.submit());
            if (chkAll)  chkAll.addEventListener('change', () => {
                document.querySelectorAll('tbody input[type="checkbox"].row-check').forEach(chk => chk.checked = chkAll.checked);
            });

            // Menús 3 puntos
            document.querySelectorAll('[data-menu-toggle]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const id = btn.getAttribute('data-menu-toggle');
                    const menu = document.getElementById(id);
                    if (!menu) return;
                    document.querySelectorAll('[id^="menu-"]').forEach(m => { if (m !== menu) m.classList.add('hidden'); });
                    menu.classList.toggle('hidden');
                });
            });
            document.addEventListener('click', () => {
                document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
            });

            // Abrir/cerrar modales (semáforo y ver)
            document.querySelectorAll('[data-modal-open]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-modal-open');
                    const m  = document.getElementById(id);
                    if (m) m.classList.remove('hidden');
                });
            });
            document.querySelectorAll('[data-modal-close]').forEach(el => {
                el.addEventListener('click', () => {
                    const id = el.getAttribute('data-modal-close');
                    const m  = document.getElementById(id);
                    if (m) m.classList.add('hidden');
                });
            });

            // Modal global helpers
            const alertModal = document.getElementById('alert-modal');
            const alertText  = document.getElementById('alert-modal-text');
            function showAlert(msg) { alertText.textContent = msg; alertModal.classList.remove('hidden'); }
            document.querySelectorAll('[data-alert-close]').forEach(b => {
                b.addEventListener('click', () => alertModal.classList.add('hidden'));
            });

            // ===== Calificar (APTO / NO APTO) con modal de novedad =====
            const CALIFICAR_URL = "{{ route('usuarios.calificar') }}";
            const CSRF = (document.querySelector('meta[name=\"csrf-token\"]') || {}).content || "{{ csrf_token() }}";

            // marcador al lado del nombre
            function paintMark(cedula, estado) {
                const row  = document.querySelector(`tr[data-cedula="${cedula}"]`);
                if (!row) return;
                const mark = row.querySelector('.mark-calif');
                if (!mark) return;
                if (estado === 'APTO') {
                    mark.className = 'mark-calif ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-700';
                    mark.textContent = '✓ APTO'; mark.title = 'Ya seleccionado: APTO';
                } else if (estado === 'NO_APTO') {
                    mark.className = 'mark-calif ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-700';
                    mark.textContent = '✕ NO APTO'; mark.title = 'Ya seleccionado: NO APTO';
                }
            }

            // modal calificar refs
            const modalCalif = document.getElementById('modal-calificar');
            const inpCedula  = document.getElementById('calif-cedula');
            const inpEstado  = document.getElementById('calif-estado');
            const selNovedad = document.getElementById('calif-novedad');
            const wrapDet    = document.getElementById('wrap-detalle');
            const txtDetalle = document.getElementById('calif-detalle');
            const errDetalle = document.getElementById('detalle-error');

            selNovedad.addEventListener('change', () => {
                if (selNovedad.value === 'NOVEDAD') {
                    wrapDet.classList.remove('hidden'); txtDetalle.focus();
                } else {
                    wrapDet.classList.add('hidden'); txtDetalle.value = ''; errDetalle.classList.add('hidden');
                }
            });

            // Abre el modal de novedad cuando se elige APTO/NO_APTO
            document.querySelectorAll('[data-action]').forEach(a => {
                a.addEventListener('click', (e) => {
                    e.preventDefault(); e.stopPropagation();
                    const action = a.getAttribute('data-action'); // ver | apto | no_apto
                    const cedula = a.getAttribute('data-cedula');
                    if (action === 'ver') {
                        const viewId = a.getAttribute('data-view-id');
                        const modal  = document.getElementById(viewId);
                        if (modal) modal.classList.remove('hidden');
                        const menu = a.closest('[id^="menu-"]'); if (menu) menu.classList.add('hidden');
                        return;
                    }
                    // setea estado y cédula
                    inpCedula.value = cedula;
                    inpEstado.value = (action === 'apto') ? 'APTO' : 'NO_APTO';
                    selNovedad.value = 'SIN_NOVEDAD';
                    wrapDet.classList.add('hidden'); txtDetalle.value = ''; errDetalle.classList.add('hidden');

                    modalCalif.classList.remove('hidden');

                    // cierra el menú
                    const menu = a.closest('[id^="menu-"]'); if (menu) menu.classList.add('hidden');
                });
            });

            // Enviar confirmación
            document.getElementById('form-calificar').addEventListener('submit', async (e) => {
                e.preventDefault();
                const cedula  = inpCedula.value;
                const estado  = inpEstado.value;
                const novedad = selNovedad.value;
                const detalle = txtDetalle.value.trim();

                if (novedad === 'NOVEDAD' && !detalle) {
                    errDetalle.classList.remove('hidden'); txtDetalle.focus(); return;
                } else {
                    errDetalle.classList.add('hidden');
                }

                try {
                    const res = await fetch(CALIFICAR_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': CSRF,'Accept':'application/json' },
                        body: JSON.stringify({ cedula, estado, novedad, detalle_novedad: detalle })
                    });
                    const data = await res.json();

                    if (data.status === 'ok') {
                        showAlert(`Usuario ${cedula} agregado al carrito como ${data.estado}${novedad==='NOVEDAD' ? ' (con novedad)' : ''}.`);
                        paintMark(cedula, data.estado);
                        modalCalif.classList.add('hidden');
                    } else if (data.status === 'exists') {
                        showAlert(data.message || 'Este usuario ya fue calificado.');
                    } else {
                        showAlert(data.message || 'No se pudo calificar.');
                    }
                } catch (err) {
                    showAlert('Error de red o servidor.');
                }
            });
        });
    </script>




@endsection
