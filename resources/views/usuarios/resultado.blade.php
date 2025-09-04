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
                            <a href="{{ route('usuarios.resultados', ['clear' => 1]) }}"
                               class="py-2 px-3 text-sm font-medium text-gray-900 bg-white rounded-md border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white">
                                Limpiar
                            </a>
                            <button type="button"
                                    class="py-2 px-3 text-sm font-medium text-gray-900 bg-white rounded-md border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white">
                                <svg class="mr-2 w-4 h-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                Export
                            </button>
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
                                    Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- TABLA: + columna SEMÁFORO, + menú 3 puntos --}}
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
                            // Etiquetas legibles para el modal
                            $labels = [
                              // ALERTA (rojo)
                              'alertas' => 'Alertas',
                              'alerta_devengacion' => 'Alerta Devengación',
                              'alerta_marco_legal' => 'Alerta Marco Legal',
                              // OTROS (amarillo)
                              'novedad_situacion' => 'Novedad Situación',
                              'observacion_tenencia' => 'Observación Tenencia',
                              'contrato_estudios' => 'Contrato de Estudios',
                              'pase_ucp_ccp_cpl' => 'Pase UCP/CCP/CPL',
                              // SALUD (celeste)
                              'alertas_problemas_salud' => 'Alertas Problemas de Salud',
                              'FaseMaternidadUDGA' => 'Fase Maternidad UDGA',
                              'fase_maternidad' => 'Fase Maternidad',
                              'maternidad' => 'Maternidad',
                              'enf_catast_sp' => 'Enfermedad Catastrófica SP',
                              'enf_catast_conyuge_hijos' => 'Enf. Catastrófica Cónyuge/Hijos',
                              'discapacidad_sp' => 'Discapacidad SP',
                              'discapacidad_conyuge_hijos' => 'Discapacidad Cónyuge/Hijos',
                            ];

                            $colsAlerta = ['alertas','alerta_devengacion','alerta_marco_legal'];
                            $colsOtros  = ['novedad_situacion','observacion_tenencia','contrato_estudios','pase_ucp_ccp_cpl'];
                            $colsSalud  = ['alertas_problemas_salud','FaseMaternidadUDGA','fase_maternidad','maternidad','enf_catast_sp','enf_catast_conyuge_hijos','discapacidad_sp','discapacidad_conyuge_hijos'];

                            $hasVal = function($v) {
                              if ($v === null) return false;
                              $s = strtoupper(trim((string)$v));
                              return $s !== '' && !in_array($s, ['NO','N/A','NA','0'], true);
                            };
                        @endphp

                        @forelse ($usuarios as $u)
                            @php
                                // Recolecta activaciones por categoría
                                $hitA = []; foreach($colsAlerta as $c){ $v = $u->{$c} ?? $u->{$c} ?? null; if($hasVal($u->{$c} ?? null)) $hitA[$c] = $u->{$c}; }
                                $hitO = []; foreach($colsOtros  as $c){ if($hasVal($u->{$c} ?? null)) $hitO[$c] = $u->{$c}; }
                                // Campos con mayúsculas deben accederse así:
                                $tmpFaseUDGA = $u->{'FaseMaternidadUDGA'} ?? null;
                                $hitS = [];
                                foreach($colsSalud as $c){
                                  $val = ($c === 'FaseMaternidadUDGA') ? $tmpFaseUDGA : ($u->{$c} ?? null);
                                  if($hasVal($val)) $hitS[$c] = $val;
                                }

                                // Prioridad: ALERTA > OTROS > SALUD > SIN ALERTA
                                $categoria = 'SIN ALERTA';
                                $colorDot  = 'bg-green-500';
                                if(count($hitA)){ $categoria = 'ALERTA'; $colorDot = 'bg-red-500'; }
                                elseif(count($hitO)){ $categoria = 'OTROS'; $colorDot = 'bg-yellow-400'; }
                                elseif(count($hitS)){ $categoria = 'SALUD'; $colorDot = 'bg-sky-400'; }

                                $modalId   = 'modal-' . preg_replace('/\W+/', '', (string)$u->cedula);
                                $tieneAlgo = $categoria !== 'SIN ALERTA';
                            @endphp

                            <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="px-3 py-3">
                                    <input type="checkbox" onclick="event.stopPropagation()"
                                           class="row-check w-4 h-4 text-primary-600 bg-gray-100 rounded border-gray-300 focus:ring-0 focus:outline-none dark:bg-gray-700 dark:border-gray-600">
                                </td>
                                <td class="px-3 py-3 font-mono">{{ $u->cedula }}</td>
                                <td class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $u->apellidos_nombres }}</td>
                                <td class="px-3 py-3">{{ $u->grado }}</td>

                                {{-- SEMÁFORO --}}
                                <td class="px-3 py-3">
                                    @if($tieneAlgo)
                                        <button type="button" data-modal-open="{{ $modalId }}"
                                                class="inline-flex items-center gap-2 px-2 py-1 rounded-md bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none">
                                            <span class="inline-block w-3 h-3 rounded-full {{ $colorDot }}"></span>
                                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $categoria }}</span>
                                        </button>

                                        {{-- MODAL --}}
                                        <div id="{{ $modalId }}" class="fixed inset-0 z-40 hidden">
                                            <div data-modal-close="{{ $modalId }}" class="absolute inset-0 bg-black/40"></div>
                                            <div class="relative z-50 mx-auto mt-24 w-[92%] max-w-lg rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl">
                                                <div class="flex items-center justify-between px-4 py-3 border-b dark:border-gray-700">
                                                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                                        Detalle de {{ $categoria }} — {{ $u->apellidos_nombres }} ({{ $u->cedula }})
                                                    </h3>
                                                    <button type="button" data-modal-close="{{ $modalId }}" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                                                        ✕
                                                    </button>
                                                </div>
                                                <div class="p-4 space-y-4 text-sm">
                                                    @if(count($hitA))
                                                        <div>
                                                            <div class="font-semibold text-red-600 dark:text-red-400 mb-1">ALERTA</div>
                                                            <ul class="list-disc pl-5 space-y-1">
                                                                @foreach($hitA as $k=>$v)
                                                                    <li><span class="font-medium">{{ $labels[$k] ?? $k }}:</span> {{ $v }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    @if(count($hitO))
                                                        <div>
                                                            <div class="font-semibold text-yellow-600 dark:text-yellow-400 mb-1">OTROS</div>
                                                            <ul class="list-disc pl-5 space-y-1">
                                                                @foreach($hitO as $k=>$v)
                                                                    <li><span class="font-medium">{{ $labels[$k] ?? $k }}:</span> {{ $v }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    @if(count($hitS))
                                                        <div>
                                                            <div class="font-semibold text-sky-600 dark:text-sky-400 mb-1">SALUD</div>
                                                            <ul class="list-disc pl-5 space-y-1">
                                                                @foreach($hitS as $k=>$v)
                                                                    <li><span class="font-medium">{{ $labels[$k] ?? $k }}:</span> {{ $v }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="px-4 py-3 border-t dark:border-gray-700 text-right">
                                                    <button type="button" data-modal-close="{{ $modalId }}"
                                                            class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-sm text-gray-800 dark:text-gray-200">
                                                        Cerrar
                                                    </button>
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

                                {{-- ACCIONES: 3 puntos (VER, APTO, NO APTO) --}}
                                <td class="px-3 py-3 relative">
                                    <button type="button"
                                            class="inline-flex items-center p-1 text-sm text-gray-500 hover:text-gray-800 hover:bg-gray-200 rounded-md focus:outline-none dark:text-gray-400 dark:hover:text-gray-100 dark:hover:bg-gray-700"
                                            data-menu-toggle="menu-{{ $u->cedula }}">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg>
                                    </button>
                                    <div id="menu-{{ $u->cedula }}" class="hidden absolute right-0 z-20 mt-2 w-40 bg-white rounded-md shadow-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600">
                                        <ul class="py-1 text-sm">
                                            <li><a href="#" data-action="ver"     data-cedula="{{ $u->cedula }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200">VER</a></li>
                                            <li><a href="#" data-action="apto"    data-cedula="{{ $u->cedula }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200">APTO</a></li>
                                            <li><a href="#" data-action="no_apto" data-cedula="{{ $u->cedula }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200">NO APTO</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-6 text-center text-gray-600 dark:text-gray-300">
                                    No hay resultados con los filtros actuales.
                                </td>
                            </tr>
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

    {{-- JS: autosubmit, 3 puntos, y modales --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form    = document.getElementById('filtros-form');
            const estado  = document.getElementById('filtro-estado');
            const alertas = document.getElementById('filtro-alertas');
            const chkAll  = document.getElementById('checkbox-all');

            if (estado)  estado.addEventListener('change', () => form.submit());
            if (alertas) alertas.addEventListener('change', () => form.submit());
            if (chkAll)  chkAll.addEventListener('change', () => {
                document.querySelectorAll('tbody input[type="checkbox"].row-check').forEach(chk => { chk.checked = chkAll.checked; });
            });

            // Toggle menú 3 puntos
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

            // Modales (abrir/cerrar)
            document.querySelectorAll('[data-modal-open]').forEach(btn => {
                btn.addEventListener('click', (e) => {
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
        });
    </script>
@endsection
