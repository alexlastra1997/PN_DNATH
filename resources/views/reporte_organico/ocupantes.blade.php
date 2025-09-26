@extends('layouts.app')

@section('content')
    @php
        // Conjunto para validar grados permitidos (para la alerta en la tabla derecha)
        $allowedSet = collect($gradosPermitidos ?? [])
            ->map(fn($g) => mb_strtoupper(trim($g)))
            ->flip();

        $hayAlerta = collect($ocupantes ?? [])->contains(function($x) use ($allowedSet){
            $g = mb_strtoupper(trim($x->grado ?? ''));
            return $g === '' || !$allowedSet->has($g);
        });

        $primero = ($infoCargo ?? collect())->first();
    @endphp

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 text-gray-900 ">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl md:text-2xl font-semibold tracking-tight text-gray-900 ">
                Ocupantes de <span class="font-bold">{{ $nomenclatura }}</span> — <span class="font-bold">{{ $cargo }}</span>
            </h2>

            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg
                  text-white bg-gray-700 hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-300
                  dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                ← Volver
            </a>
        </div>

        {{-- Grid 30% / 70% en ≥ lg --}}
        <div class="grid grid-cols-1 lg:grid-cols-10 gap-4">
            {{-- Columna izquierda: 30% (cards informativos) --}}
            <div class="lg:col-span-3">
                <h3 class="text-base font-medium mb-2 text-gray-900 ">Información del cargo (Orgánico)</h3>

                @if(($infoCargo ?? collect())->count())
                    {{-- Card principal con Servicio, Nomenclatura, Cargo y Subsistema --}}
                    <div class="rounded-xl border border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 p-4 mb-3">
                        <div class="space-y-3">
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-gray-600 dark:text-gray-300">Servicio</p>
                                <p class="mt-0.5 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $primero->servicio_organico ?? '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-gray-600 dark:text-gray-300">Nomenclatura</p>
                                <div class="mt-0.5 w-full overflow-x-auto no-scrollbar">
                                    <p class="inline-block text-sm font-medium whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ $primero->nomenclatura_organico ?? '—' }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-gray-600 dark:text-gray-300">Cargo</p>
                                <p class="mt-0.5 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $primero->cargo_organico ?? '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[11px] uppercase tracking-wide text-gray-600 dark:text-gray-300">Subsistema</p>
                                <p class="mt-0.5 text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $primero->subsistema ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Dos cards pequeños --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-4 rounded-xl border border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700">
                            <p class="text-[11px] text-gray-600 dark:text-gray-300 mb-2">Grado(s) requerido(s)</p>
                            <div class="flex flex-wrap gap-1.5">
                                @php $chips = collect($gradosPermitidos ?? [])->filter(); @endphp
                                @forelse($chips as $g)
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded
                                             bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">{{ $g }}</span>
                                @empty
                                    <span class="text-[12px] text-gray-400">—</span>
                                @endforelse
                            </div>
                        </div>

                        @php
                            $persOrg = $primero->personal_organico ?? '—';
                            $nroIdeal = $primero->numero_organico_ideal ?? null;
                        @endphp
                        <div class="p-4 rounded-xl border border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700">
                            <p class="text-[11px] text-gray-600 dark:text-gray-300">Personal orgánico</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $persOrg }}</p>
                            <p class="mt-3 text-[11px] text-gray-600 dark:text-gray-300">N° orgánico ideal</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $nroIdeal !== null ? number_format((float)$nroIdeal, 0, ',', '.') : '—' }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center p-4 text-sm text-amber-800 rounded-lg bg-amber-50
                            dark:bg-gray-800 dark:text-amber-300" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9 4a1 1 0 1 1 2 0v6a1 1 0 1 1-2 0V4Zm1 11.75a1.75 1.75 0 1 1 0-3.5 1.75 1.75 0 0 1 0 3.5Z"/>
                        </svg>
                        No se encontró información orgánica para este cargo en esta nomenclatura base.
                    </div>
                @endif
            </div>

            {{-- Columna derecha: 70% (tabla de ocupantes) --}}
            <div class="lg:col-span-7">
                <h3 class="text-base font-medium mb-2 text-gray-900 dark:text-gray-100">Ocupantes</h3>

                <div class="relative overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-xs md:text-sm text-left rtl:text-right text-gray-700 dark:text-gray-300">
                        <thead class="text-[11px] uppercase bg-gray-100 text-gray-700
                                  dark:bg-gray-700 dark:text-gray-100">
                        <tr>
                            <th class="px-6 py-3">Cédula</th>
                            <th class="px-6 py-3">Grado</th>
                            <th class="px-6 py-3">Apellidos y Nombres</th>
                            <th class="px-6 py-3">Estado de Traslado</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($ocupantes as $o)
                            @php
                                $gradoNorm = mb_strtoupper(trim($o->grado ?? ''));
                                $noCumple  = $gradoNorm === '' ? true : !$allowedSet->has($gradoNorm);
                            @endphp
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-gray-100">
                                    {{ $o->cedula }}
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $o->grado ?: '—' }}</span>
                                        @if($noCumple)
                                            <span class="inline-flex items-center text-xs font-medium me-2 px-2.5 py-0.5 rounded
                                                         text-yellow-800 bg-yellow-100
                                                         dark:text-yellow-200 dark:bg-yellow-900/50"
                                                  title="Grado no coincide con lo requerido para este cargo">
                                                ⚠️
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3">{{ $o->apellidos_nombres }}</td>
                                <td class="px-6 py-3">{{ $o->estado_efectivo }}</td>
                            </tr>
                        @empty
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-300">
                                    No hay ocupantes.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($hayAlerta)
                    <div class="flex items-center p-4 mt-3 text-sm text-yellow-800 rounded-lg bg-yellow-50
                            dark:bg-gray-800 dark:text-yellow-300" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9 4a1 1 0 1 1 2 0v6a1 1 0 1 1-2 0V4Zm1 11.75a1.75 1.75 0 1 1 0-3.5 1.75 1.75 0 0 1 0 3.5Z"/>
                        </svg>
                        ⚠️ La advertencia se muestra solo para servidores cuyo <strong>grado</strong> no está dentro de los grados permitidos del cargo.
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Helpers para ocultar scrollbar fino en contenedores horizontales --}}
    <style>
        .no-scrollbar::-webkit-scrollbar{display:none}
        .no-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
    </style>

    <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.min.js"></script>
@endsection
