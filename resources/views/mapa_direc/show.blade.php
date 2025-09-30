@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 py-6">
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-8">

            {{-- Encabezado --}}
            <div class="mb-6 flex items-start justify-between gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Ocupantes del Orgánico</h1>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        <span class="font-medium">Servicio:</span> {{ $ro->servicio_organico ?? '—' }} &middot;
                        <span class="font-medium">Nomenclatura:</span> {{ $ro->nomenclatura_organico ?? '—' }} &middot;
                        <span class="font-medium">Cargo:</span> {{ $ro->cargo_organico ?? '—' }} &middot;
                        <span class="font-medium">Grado:</span> {{ $ro->grado_organico ?? '—' }}
                    </p>
                </div>
                <div>
                    <a href="{{ url()->previous() }}"
                       class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        ← Volver
                    </a>
                </div>
            </div>

            {{-- Lista de ocupantes --}}
            @if(($ocupantes ?? collect())->count() > 0)
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr class="text-gray-800 dark:text-gray-100">
                            <th class="px-3 py-2 text-left">Cédula</th>
                            <th class="px-3 py-2 text-left">Apellidos y Nombres</th>
                            <th class="px-3 py-2 text-left">Grado</th>
                            <th class="px-3 py-2 text-left">Promoción</th>
                            <th class="px-3 py-2 text-left">Nomenclatura Efectiva</th>
                            <th class="px-3 py-2 text-left">Función Efectiva</th>
                            <th class="px-3 py-2 text-left">Estado Efectivo</th>
                            <th class="px-3 py-2 text-left">Fecha Efectiva</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($ocupantes as $u)
                            <tr class="text-gray-900 dark:text-gray-100">
                                <td class="px-3 py-2 whitespace-nowrap">{{ $u->cedula }}</td>
                                <td class="px-3 py-2">{{ $u->apellidos_nombres }}</td>
                                <td class="px-3 py-2">{{ $u->grado }}</td>
                                <td class="px-3 py-2">{{ $u->promocion }}</td>
                                <td class="px-3 py-2">{{ $u->nomenclatura_efectiva }}</td>
                                <td class="px-3 py-2">{{ $u->funcion_efectiva }}</td>
                                <td class="px-3 py-2">{{ $u->estado_efectivo }}</td>
                                <td class="px-3 py-2">{{ $u->fecha_efectiva }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-8 text-center text-gray-500 dark:text-gray-400">
                    No hay ocupantes asignados a esta combinación exacta.
                </div>
            @endif

        </div>
    </section>
@endsection
