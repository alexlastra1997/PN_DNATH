@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 py-3 sm:py-5">
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-12">

            {{-- ====== FILTROS (layout mejorado) ====== --}}
            <div class="mb-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                <div class="grid grid-cols-12 gap-4 items-end">
                    {{-- Columna: multiselect (8/12 en md+) --}}
                    <div class="col-span-12 md:col-span-8">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Filtrar por columnas con ALERTA (múltiple)
                        </label>
                        <select id="filtro-alertas" multiple size="8"
                                class="w-full h-40 md:h-44 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100 p-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="contrato_estudios">contrato_estudios</option>
                            <option value="enf_catast_sp">enf_catast_sp</option>
                            <option value="enf_catast_conyuge_hijos">enf_catast_conyuge_hijos</option>
                            <option value="discapacidad_sp">discapacidad_sp</option>
                            <option value="discapacidad_conyuge_hijos">discapacidad_conyuge_hijos</option>
                            <option value="alertas">alertas</option>
                            <option value="alerta_devengacion">alerta_devengacion</option>
                            <option value="alerta_marco_legal">alerta_marco_legal</option>
                            <option value="alertas_problemas_salud">alertas_problemas_salud</option>
                            <option value="novedad_situacion">novedad_situacion</option>
                            <option value="observacion_tenencia">observacion_tenencia</option>
                            <option value="FaseMaternidadUDGA">FaseMaternidadUDGA</option>
                            <option value="fase_maternidad">fase_maternidad</option>
                        </select>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                            Consejo: usa Ctrl/Cmd o Shift para selección múltiple.
                        </p>
                    </div>

                    {{-- Columna: “Mostrar” (3/12 en md+) --}}
                    <div class="col-span-12 md:col-span-3">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-200 mb-1">Mostrar</label>
                        <select id="filtro-estado"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-gray-100 p-2 h-10 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="todos">Todos</option>
                            <option value="alerta">Solo con ALERTA</option>
                            <option value="activo">Solo Activos</option>
                        </select>
                    </div>

                    {{-- Columna: botones (1/12 en md+, alineados a derecha) --}}
                    <div class="col-span-12 md:col-span-1 flex md:flex-col gap-2 md:items-end md:justify-end">
                        <button id="btn-aplicar-filtros"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-md bg-gray-900 text-white text-sm hover:bg-gray-800 w-full">
                            Aplicar
                        </button>
                        <button id="btn-limpiar-filtros"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-600 w-full">
                            Limpiar
                        </button>
                    </div>
                </div>

                <div class="mt-3 text-xs text-gray-700 dark:text-gray-200">
                    Mostrando <span id="contador-visibles">—</span> de {{ method_exists($usuarios,'count') ? $usuarios->count() : 0 }} filas en esta página
                    (Total: {{ method_exists($usuarios,'total') ? number_format($usuarios->total()) : 0 }}).
                </div>
            </div>


            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                {{-- Header --}}
                <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4 border-b dark:border-gray-700">
                    <div class="w-full flex items-center space-x-3">
                        <h5 class="dark:text-white font-semibold">Usuarios del documento</h5>
                        <div class="text-gray-400 font-medium">
                            {{ method_exists($usuarios, 'total') ? number_format($usuarios->total()) : 0 }} resultados
                        </div>
                        <div data-tooltip-target="results-tooltip">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">More info</span>
                        </div>
                    </div>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="p-4">
                                <div class="flex items-center">
                                    <input id="checkbox-all" type="checkbox" class="w-4 h-4 text-primary-600 bg-gray-100 rounded border-gray-300 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="checkbox-all" class="sr-only">checkbox</label>
                                </div>
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <span class="sr-only">Expand/Collapse Row</span>
                            </th>
                            <th scope="col" class="px-4 py-3 min-w-[14rem]">Apellidos y Nombres</th>
                            <th scope="col" class="px-4 py-3 min-w-[10rem]">
                                Cédula
                                <svg class="h-4 w-4 ml-1 inline-block" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" />
                                </svg>
                            </th>
                            <th scope="col" class="px-4 py-3 min-w-[6rem]">
                                Grado
                                <svg class="h-4 w-4 ml-1 inline-block" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" />
                                </svg>
                            </th>
                            <th scope="col" class="px-4 py-3 min-w-[10rem]">Provincia trabaja</th>
                            <th scope="col" class="px-4 py-3 min-w-[10rem]">Provincia vive</th>
                            <th scope="col" class="px-4 py-3 min-w-[7rem]">Estado</th>
                        </tr>
                        </thead>
                        <tbody data-accordion="table-column">
                        @forelse($usuarios as $idx => $u)
                            @php
                                $rowId = "table-column-header-{$idx}";
                                $bodyId = "table-column-body-{$idx}";
                                // Mapa de alertas:
                                $mapaAlertas = [
                                    'contrato_estudios'         => $u->contrato_estudios ?? null,
                                    'enf_catast_sp'             => $u->enf_catast_sp ?? null,
                                    'enf_catast_conyuge_hijos'  => $u->enf_catast_conyuge_hijos ?? null,
                                    'discapacidad_sp'           => $u->discapacidad_sp ?? null,
                                    'discapacidad_conyuge_hijos'=> $u->discapacidad_conyuge_hijos ?? null,
                                    'alertas'                   => $u->alertas ?? null,
                                    'alerta_devengacion'        => $u->alerta_devengacion ?? null,
                                    'alerta_marco_legal'        => $u->alerta_marco_legal ?? null,
                                    'alertas_problemas_salud'   => $u->alertas_problemas_salud ?? null,
                                    'novedad_situacion'         => $u->novedad_situacion ?? null,
                                    'observacion_tenencia'      => $u->observacion_tenencia ?? null,
                                    'FaseMaternidadUDGA'        => $u->FaseMaternidadUDGA ?? null,
                                    'fase_maternidad'           => $u->fase_maternidad ?? null,
                                ];
                                $esAlerta = function($val) {
                                    if (!isset($val)) return false;
                                    $s = trim((string)$val);
                                    return $s !== '' && $s !== '0' && strtoupper($s) !== 'NO' && strtoupper($s) !== 'N/A' && strtoupper($s) !== 'NULL';
                                };
                                $hayAlerta = collect($mapaAlertas)->some(fn($v) => $esAlerta($v));
                            @endphp

                            {{-- Fila principal --}}
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer transition"
                                id="{{ $rowId }}"
                                data-accordion-target="#{{ $bodyId }}"
                                aria-expanded="false"
                                aria-controls="{{ $bodyId }}">
                                <td class="px-4 py-3 w-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" onclick="event.stopPropagation()"
                                               class="w-4 h-4 text-primary-600 bg-gray-100 rounded border-gray-300 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    </div>
                                </td>
                                <td class="p-3 w-4">
                                    <svg data-accordion-icon class="w-6 h-6 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </td>
                                <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $u->apellidos_nombres }}
                                </th>
                                <td class="px-4 py-3">{{ $u->cedula }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $u->grado }}</td>
                                <td class="px-4 py-3">{{ $u->provincia_trabaja }}</td>
                                <td class="px-4 py-3">{{ $u->provincia_vive }}</td>

                                {{-- ESTADO: Activo/ALERTA --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if(!$hayAlerta)
                                        <div class="w-fit bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-100 dark:text-green-800">
                                            Activo
                                        </div>
                                    @else
                                        <div class="w-fit bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-100 dark:text-red-800">
                                            ALERTA
                                        </div>
                                    @endif
                                </td>
                            </tr>

                            {{-- Fila expandida --}}
                            <tr class="hidden flex-1 overflow-x-auto w-full" id="{{ $bodyId }}" aria-labelledby="{{ $rowId }}">
                                <td class="p-4 border-b dark:border-gray-700" colspan="8">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                            <h6 class="mb-2 text-base leading-none font-medium text-gray-900 dark:text-white">Datos básicos</h6>
                                            <div class="text-gray-600 dark:text-gray-300 text-sm">
                                                <div><span class="font-medium">Cédula:</span> {{ $u->cedula }}</div>
                                                <div><span class="font-medium">Grado:</span> {{ $u->grado }}</div>
                                                <div><span class="font-medium">Promoción:</span> {{ $u->promocion ?? '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                            <h6 class="mb-2 text-base leading-none font-medium text-gray-900 dark:text-white">Ubicación</h6>
                                            <div class="text-gray-600 dark:text-gray-300 text-sm">
                                                <div><span class="font-medium">Trabaja:</span> {{ $u->provincia_trabaja ?? '—' }}</div>
                                                <div><span class="font-medium">Vive:</span> {{ $u->provincia_vive ?? '—' }}</div>
                                                <div><span class="font-medium">Domicilio:</span> {{ $u->domicilio ?? '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                            <h6 class="mb-2 text-base leading-none font-medium text-gray-900 dark:text-white">Estado</h6>
                                            @if($hayAlerta)
                                                <span class="inline-flex items-center text-xs font-medium px-2.5 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-100 dark:text-red-800">ALERTA</span>
                                            @else
                                                <span class="inline-flex items-center text-xs font-medium px-2.5 py-0.5 rounded bg-green-100 text-green-800 dark:bg-green-100 dark:text-green-800">Activo</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Tabla de Alertas --}}
                                    <div class="overflow-x-auto mt-4">
                                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                                            <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                            <tr>
                                                <th class="px-3 py-2">Campo</th>
                                                <th class="px-3 py-2">Valor</th>
                                                <th class="px-3 py-2">Estado</th>
                                            </tr>
                                            </thead>
                                            <tbody class="divide-y dark:divide-gray-700">
                                            @foreach($mapaAlertas as $campo => $valor)
                                                @php $alerta = $esAlerta($valor); @endphp
                                                <tr class="@if($alerta) bg-red-50 dark:bg-red-900/30 @endif">
                                                    <td class="px-3 py-2 font-medium">{{ $campo }}</td>
                                                    <td class="px-3 py-2">{{ $valor ?? '—' }}</td>
                                                    <td class="px-3 py-2">
                                                        @if($alerta)
                                                            <span class="inline-flex items-center text-xs font-medium px-2.5 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-100 dark:text-red-800">ALERTA</span>
                                                        @else
                                                            <span class="inline-flex items-center text-xs font-medium px-2.5 py-0.5 rounded bg-green-100 text-green-800 dark:bg-green-100 dark:text-green-800">OK</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Botones --}}
                                    <div class="flex items-center space-x-3 mt-4">
                                        <button type="button" class="py-2 px-3 flex items-center text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                            Ver ficha
                                        </button>
                                        <button type="button" class="py-2 px-3 flex items-center text-sm font-medium text-center text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                            Acciones
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                    No hay usuarios para mostrar. Sube un documento en <a href="{{ route('usuarios.opciones') }}" class="underline">Usuarios → Opciones</a>.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer / Navegación --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 px-4 pt-3 pb-4" aria-label="Table navigation">
                    <div class="text-xs flex items-center space-x-5">
                        <div>
                            <div class="text-gray-500 dark:text-gray-400 mb-1">Resultados</div>
                            <div class="dark:text-white font-medium">
                                {{ $usuarios->firstItem() ?? 0 }}–{{ $usuarios->lastItem() ?? 0 }} de {{ number_format($usuarios->total() ?? 0) }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        {{ $usuarios->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- JS mínimo para "select all" sin romper accordion --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chkAll = document.getElementById('checkbox-all');
            if (chkAll) {
                chkAll.addEventListener('change', () => {
                    document.querySelectorAll('tbody input[type="checkbox"]').forEach(chk => { chk.checked = chkAll.checked; });
                });
            }
        });
    </script>
@endsection
