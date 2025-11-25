{{-- resources/views/agregados_policiales.blade.php --}}
@extends('layouts.app')

@section('content')
    @php
        use Illuminate\Support\Str;
        $primerPais = array_key_first($porPais);
    @endphp

    <section class="bg-gray-50 py-6">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <h1 class="text-xl font-bold text-gray-900">
                Agregados / Funciones en el Exterior – Orgánico vs Efectivo
            </h1>

            {{-- ===== Tabs de Flowbite por país ===== --}}
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                <ul
                    class="flex flex-wrap -mb-px text-sm font-medium text-center"
                    id="countryTabs"
                    data-tabs-toggle="#countryTabsContent"
                    role="tablist"
                >
                    @foreach($porPais as $pais => $_rows)
                        @php $slug = Str::slug($pais ?: 'sin-pais', '-'); @endphp
                        <li class="me-2" role="presentation">
                            <button
                                class="inline-block p-3 border-b-2 rounded-t-lg
              @if($pais === $primerPais)
                border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400
              @else
                border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300
              @endif"
                                id="tab-{{ $slug }}"
                                data-tabs-target="#panel-{{ $slug }}"
                                type="button"
                                role="tab"
                                aria-controls="panel-{{ $slug }}"
                                aria-selected="{{ $pais === $primerPais ? 'true' : 'false' }}"
                            >
                                {{ $pais }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div id="countryTabsContent">
                @foreach($porPais as $pais => $rows)
                    @php $slug = Str::slug($pais ?: 'sin-pais', '-'); @endphp
                    <div id="panel-{{ $slug }}" role="tabpanel" aria-labelledby="tab-{{ $slug }}"
                         class="@if($pais !== $primerPais) hidden @endif">

                        {{-- ===== Tabla 1: CARGOS (resumen) ===== --}}
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow mb-6">
                            <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-300">
                                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <tr>
                                    <th class="px-4 py-3">Servicio</th>
                                    <th class="px-4 py-3">Nomenclatura</th>
                                    <th class="px-4 py-3">Cargo / Función</th>
                                    <th class="px-4 py-3 text-center">Aprobado</th>
                                    <th class="px-4 py-3 text-center">Efectivo</th>
                                    <th class="px-4 py-3 text-center">Estado</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($rows as $row)
                                    @php
                                        $estadoClass = match($row->estado) {
                                          'COMPLETO' => 'text-blue-600 dark:text-blue-400',
                                          'VACANTE'  => 'text-yellow-500 dark:text-yellow-400',
                                          'EXCEDENTE'=> 'text-red-600 dark:text-red-400',
                                          default    => 'text-gray-700 dark:text-gray-300'
                                        };
                                    @endphp
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <td class="px-4 py-3">{{ $row->servicio_organico }}</td>
                                        <td class="px-4 py-3">{{ $row->nomenclatura_organico }}</td>
                                        <td class="px-4 py-3">{{ $row->cargo_organico }}</td>
                                        <td class="px-4 py-3 text-center font-medium">{{ (int)$row->organico_aprobado }}</td>
                                        <td class="px-4 py-3 text-center font-medium">{{ (int)$row->organico_efectivo }}</td>
                                        <td class="px-4 py-3 text-center font-bold {{ $estadoClass }}">{{ $row->estado }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                            Sin filas para {{ $pais }}.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- ===== Tabla 2: OCUPANTES (para los cargos de este país) ===== --}}
                        <h3 class="text-base font-semibold text-gray-900 mb-2">Ocupantes</h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow">
                            <table class="min-w-full text-sm text-left">
                                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <tr>
                                    <th class="px-3 py-2">Cédula</th>
                                    <th class="px-3 py-2">Grado</th>
                                    <th class="px-3 py-2">Apellidos Nombres</th>
                                    <th class="px-3 py-2">Nomenclatura efectiva</th>
                                    <th class="px-3 py-2">Función efectiva</th>
                                    <th class="px-3 py-2">Estado efectivo</th>
                                    <th class="px-3 py-2 text-center">Info</th>
                                    <th class="px-3 py-2 text-center">Alerta</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $hayOcupantes = false; @endphp
                                @foreach($rows as $row)
                                    @php
                                        $key   = md5($row->servicio_organico.'|'.$row->nomenclatura_organico.'|'.$row->cargo_organico);
                                        $lista = $ocupantes[$key] ?? collect();
                                    @endphp

                                    @forelse($lista as $u)
                                        @php
                                            $hayOcupantes = true;
                                            $noCumple = Str::upper(trim($u->funcion_efectiva))      !== Str::upper(trim($row->cargo_organico))
                                                     || Str::upper(trim($u->nomenclatura_efectiva)) !== Str::upper(trim($row->nomenclatura_organico));

                                            $badgeOk    = 'inline-flex items-center rounded-full bg-green-100 text-green-700 px-2 py-0.5 text-xs font-semibold';
                                            $badgeAlert = 'inline-flex items-center rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-xs font-semibold';

                                            $modalId = 'info-usuario-'.$u->id.'-'.$slug;
                                        @endphp

                                        <tr class="border-b border-gray-200 dark:border-gray-700 dark:text-gray-200">
                                            <td class="px-3 py-2">{{ $u->cedula }}</td>
                                            <td class="px-3 py-2">{{ $u->grado }}</td>
                                            <td class="px-3 py-2">{{ $u->apellidos_nombres }}</td>
                                            <td class="px-3 py-2">{{ $u->nomenclatura_efectiva }}</td>
                                            <td class="px-3 py-2">{{ $u->funcion_efectiva }}</td>
                                            <td class="px-3 py-2">{{ $u->estado_efectivo }}</td>

                                            {{-- Botón Info (modal) --}}
                                            <td class="px-3 py-2 text-center">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center rounded-md bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                                    data-modal-target="{{ $modalId }}"
                                                    data-modal-toggle="{{ $modalId }}"
                                                    title="Información del servidor"
                                                >
                                                    Ver
                                                </button>
                                            </td>

                                            {{-- Alerta de cumplimiento --}}
                                            <td class="px-3 py-2 text-center">
                                                @if($noCumple)
                                                    <span class="{{ $badgeAlert }}">
                            <svg class="mr-1 h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.5a.75.75 0 00-1.5 0v5a.75.75 0 001.5 0v-5zm0 8a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5z" clip-rule="evenodd"/>
                            </svg>
                            No cumple
                          </span>
                                                @else
                                                    <span class="{{ $badgeOk }}">
                            <svg class="mr-1 h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.364 7.364a1 1 0 01-1.414 0L3.293 10.435a1 1 0 111.414-1.414l3.222 3.222 6.657-6.657a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            OK
                          </span>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- ===== Modal centrado (solo markup; el backdrop es global) ===== --}}
                                        <div id="{{ $modalId }}" tabindex="-1" aria-hidden="true"
                                             class="fixed inset-0 hidden z-[9999] p-4 flex items-center justify-center">
                                            <div class="relative w-full max-w-lg">
                                                <div class="relative bg-white rounded-lg shadow-2xl ring-1 ring-black/10 dark:bg-gray-800 max-h-[85vh] overflow-y-auto">
                                                    <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-700">
                                                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                                            Información del servidor
                                                        </h3>
                                                        <button type="button" class="text-gray-400 hover:text-gray-600 rounded-lg text-sm p-1.5"
                                                                data-modal-hide="{{ $modalId }}">
                                                            <span class="sr-only">Cerrar</span> ✕
                                                        </button>
                                                    </div>
                                                    <div class="p-4 space-y-2 text-sm text-gray-700 dark:text-gray-200">
                                                        <p><span class="font-semibold">Cédula:</span> {{ $u->cedula }}</p>
                                                        <p><span class="font-semibold">Apellidos y Nombres:</span> {{ $u->apellidos_nombres }}</p>
                                                        <p><span class="font-semibold">Grado:</span> {{ $u->grado }}</p>
                                                        <p><span class="font-semibold">Nomenclatura efectiva:</span> {{ $u->nomenclatura_efectiva }}</p>
                                                        <p><span class="font-semibold">Función efectiva:</span> {{ $u->funcion_efectiva }}</p>
                                                        <p><span class="font-semibold">Estado efectivo:</span> {{ $u->estado_efectivo }}</p>
                                                        <hr class="my-2 border-gray-200 dark:border-gray-700">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            Comparado contra: <strong>{{ $row->nomenclatura_organico }}</strong> /
                                                            <strong>{{ $row->cargo_organico }}</strong>
                                                        </p>
                                                    </div>
                                                    <div class="p-4 border-t dark:border-gray-700 flex justify-end">
                                                        <button type="button"
                                                                class="inline-flex items-center rounded-md bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 px-3 py-1.5 text-xs"
                                                                data-modal-hide="{{ $modalId }}">
                                                            Cerrar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- /Modal --}}
                                    @empty
                                        {{-- sin ocupantes para esta combinación --}}
                                    @endforelse
                                @endforeach

                                @if(!$hayOcupantes)
                                    <tr>
                                        <td colspan="8" class="px-3 py-3 text-center text-gray-500">
                                            No hay ocupantes para los cargos de {{ $pais }}.
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- === Backdrop global: oscurece/blur el fondo mientras un modal está abierto === --}}
    <div id="global-backdrop"
         class="fixed inset-0 hidden bg-black/70 backdrop-blur-sm z-[9998]"></div>

    {{-- Flowbite (si ya está en tu layout, puedes quitarlo aquí) --}}
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>

    <style>
        /* Oculta el overlay nativo de Flowbite para no duplicar fondos */
        [modal-backdrop], .modal-backdrop { display: none !important; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const backdrop = document.getElementById('global-backdrop');

            // Escucha todos los modales de "info" para sincronizar el backdrop
            document.querySelectorAll('[id^="info-usuario-"]').forEach((modalEl) => {
                // Cuando Flowbite muestra el modal
                modalEl.addEventListener('show.tw.modal', () => {
                    // Muestra nuestro fondo
                    backdrop.classList.remove('hidden');
                    // Hace que el click en el fondo cierre este modal concreto
                    backdrop.setAttribute('data-modal-hide', modalEl.id);
                });

                // Cuando Flowbite oculta el modal
                modalEl.addEventListener('hide.tw.modal', () => {
                    // Oculta el fondo
                    backdrop.classList.add('hidden');
                    backdrop.removeAttribute('data-modal-hide');
                });
            });

            // (Opcional) animación suave del fondo
            // Puedes añadir transition utilities en Tailwind si quieres un fade-in/out.
        });
    </script>
@endsection
