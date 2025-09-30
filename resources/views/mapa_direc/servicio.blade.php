@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 py-6">
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-8">

            {{-- Encabezado --}}
            <div class="mb-6 flex items-start justify-between gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        Detalle del Servicio
                    </h1>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Servicio:</span> {{ $servicio ?? '—' }}.
                        &nbsp;—&nbsp; Solo cargos: RECTOR, VICERRECTOR, DIRECTOR, SUBDIRECTOR, COMANDANTE, SUBCOMANDANTE, JEFE, SUBJEFE.
                    </p>
                </div>
                <div>
                    <a href="{{ route('mapa_direc.index') }}"
                       class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        ← Volver
                    </a>
                </div>
            </div>

            {{-- === Orgánico (con ESTADO por texto) === --}}
            <div class="mb-8">
                <h2 class="mb-2 text-sm font-semibold text-gray-900 dark:text-gray-100">Orgánico del servicio</h2>
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr class="text-gray-800 dark:text-gray-100">
                            <th class="px-3 py-2 text-left">Nomenclatura Orgánico</th>
                            <th class="px-3 py-2 text-left">Cargo</th>
                            <th class="px-3 py-2 text-center">Ideal</th>
                            <th class="px-3 py-2 text-center">Actual</th>
                            <th class="px-3 py-2 text-center">Estado</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($organicos as $row)
                            @php
                                $ideal  = (int)($row->organico_aprobado ?? $row->numero_organico_ideal ?? 0);
                                $actual = (int)($row->organico_efectivo ?? 0);
                                if ($actual > $ideal) {
                                  $estado = 'EXCEDIDO';  $estadoClass='text-red-600 dark:text-red-400';
                                } elseif ($actual < $ideal) {
                                  $estado = 'VACANTE';   $estadoClass='text-yellow-500 dark:text-yellow-300';
                                } else {
                                  $estado = 'COMPLETO';  $estadoClass='text-blue-600 dark:text-blue-400';
                                }
                            @endphp
                            <tr class="text-gray-900 dark:text-gray-100">
                                <td class="px-3 py-2">{{ $row->nomenclatura_organico }}</td>
                                <td class="px-3 py-2 font-medium">{{ $row->cargo_organico }}</td>
                                <td class="px-3 py-2 text-center">{{ $ideal }}</td>
                                <td class="px-3 py-2 text-center">{{ $actual }}</td>
                                <td class="px-3 py-2 text-center"><span class="font-semibold uppercase {{ $estadoClass }}">{{ $estado }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">No hay orgánico para este servicio.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- === Usuarios (botón Ver + Observaciones con alerta) === --}}
            <div>
                <h2 class="mb-2 text-sm font-semibold text-gray-900 dark:text-gray-100">Usuarios en cargos del servicio</h2>
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr class="text-gray-800 dark:text-gray-100">
                            <th class="px-2 py-2 text-left w-16">BAS</th>
                            <th class="px-3 py-2 text-left">Cédula</th>
                            <th class="px-3 py-2 text-left">Apellidos y Nombres</th>
                            <th class="px-3 py-2 text-left">Grado</th>
                            <th class="px-3 py-2 text-left">Promoción</th>
                            <th class="px-3 py-2 text-left">Función Efectiva</th>
                            <th class="px-3 py-2 text-left">Fecha Efectiva</th>
                            <th class="px-3 py-2 text-center">Observaciones</th>
                            <th class="px-3 py-2 text-left">Nomenclatura Efectiva</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($usuarios as $u)
                            @php $alerta = (int)($u->alerta_grado ?? 0) === 1; @endphp
                            <tr class="text-gray-900 dark:text-gray-100">
                                <td class="px-2 py-2">
                                    <button
                                        type="button"
                                        data-modal-open="modal-user-{{ $u->u_id }}"
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500">
                                        Ver
                                    </button>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $u->cedula }}</td>
                                <td class="px-3 py-2">{{ $u->apellidos_nombres }}</td>
                                <td class="px-3 py-2">{{ $u->grado }}</td>
                                <td class="px-3 py-2">{{ $u->promocion }}</td>
                                <td class="px-3 py-2">{{ $u->funcion_efectiva }}</td>
                                <td class="px-3 py-2">{{ $u->fecha_efectiva }}</td>
                                <td class="px-3 py-2 text-center">
                                    @if($alerta)
                                        <svg class="inline h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M9.401 1.73a1.5 1.5 0 0 1 2.598 0l7.2 12.8A1.5 1.5 0 0 1 17.9 17H2.1a1.5 1.5 0 0 1-1.298-2.47l7.2-12.8zM11 13a1 1 0 1 0-2 0 1 1 0 0 0 2 0zm-1-2a1 1 0 0 0 1-1V7a1 1 0 1 0-2 0v3a1 1 0 0 0 1 1z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-3 py-2">{{ $u->nomenclatura_efectiva }}</td>
                            </tr>

                            {{-- Modal Ficha del Servidor --}}
                            <div id="modal-user-{{ $u->u_id }}" class="modal-backdrop fixed inset-0 z-50 hidden bg-black/60 p-4">
                                <div class="mx-auto max-w-2xl rounded-xl bg-white dark:bg-gray-800 shadow-xl">
                                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Ficha del Servidor Policial</h3>
                                        <button type="button" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white" data-modal-close="modal-user-{{ $u->u_id }}">✕</button>
                                    </div>
                                    <div class="px-5 py-4 text-sm text-gray-900 dark:text-gray-100 space-y-3">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div><span class="font-medium">Cédula:</span> {{ $u->cedula }}</div>
                                            <div><span class="font-medium">Grado:</span> {{ $u->grado }}</div>
                                            <div class="col-span-2"><span class="font-medium">Nombres:</span> {{ $u->apellidos_nombres }}</div>
                                            <div><span class="font-medium">Promoción:</span> {{ $u->promocion }}</div>
                                            <div><span class="font-medium">Fecha efectiva:</span> {{ $u->fecha_efectiva }}</div>
                                            <div class="col-span-2"><span class="font-medium">Función efectiva:</span> {{ $u->funcion_efectiva }}</div>
                                            <div class="col-span-2"><span class="font-medium">Nomenclatura efectiva:</span> {{ $u->nomenclatura_efectiva }}</div>
                                        </div>

                                        @if($alerta)
                                            <div class="mt-2 rounded-md border border-red-300/60 dark:bg-red-950/30 text-red-700 dark:text-red-300 px-3 py-2">
                                                <div class="font-semibold">El grado no coincide con el grado orgánico requerido para su cargo.</div>
                                                <div>Grados válidos (orgánico): <span class="font-mono">{{ $u->grados_validos ?? '—' }}</span></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                                        <button type="button" class="inline-flex items-center rounded-md px-4 py-1.5 text-sm font-medium bg-gray-900 text-white dark:bg-gray-700 hover:bg-gray-700 dark:hover:bg-gray-600" data-modal-close="modal-user-{{ $u->u_id }}">
                                            Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            {{-- /Modal --}}

                        @empty
                            <tr><td colspan="9" class="px-3 py-8 text-center text-gray-500 dark:text-gray-400">No hay usuarios ocupando cargos para este servicio.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>

    {{-- JS mínimo para abrir/cerrar modales (sin dependencias) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const open = id => document.getElementById(id)?.classList.remove('hidden');
            const close = id => document.getElementById(id)?.classList.add('hidden');

            document.querySelectorAll('[data-modal-open]').forEach(btn => {
                btn.addEventListener('click', () => open(btn.dataset.modalOpen));
            });
            document.querySelectorAll('[data-modal-close]').forEach(btn => {
                btn.addEventListener('click', () => close(btn.dataset.modalClose));
            });
            document.querySelectorAll('.modal-backdrop').forEach(bg => {
                bg.addEventListener('click', (e) => { if (e.target === bg) bg.classList.add('hidden'); });
            });
        });
    </script>
@endsection
