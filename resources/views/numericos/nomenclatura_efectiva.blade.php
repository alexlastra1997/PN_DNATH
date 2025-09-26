{{-- resources/views/numericos/nomenclatura_efectiva.blade.php --}}
@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 py-6">
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-8">

            {{-- Encabezado --}}
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                            Numéricos por Nomenclatura Efectiva
                        </h1>
                        <p class="text-xs text-gray-600 dark:text-gray-300">
                            Fuente: <span class="font-mono">usuarios.nomenclatura_efectiva</span>
                        </p>
                        {{-- Chip opcional: Solo NDESC --}}
                        @if(!empty($soloNdesc))
                            <div class="mt-3 inline-flex items-center gap-2 rounded-full border border-emerald-300/60 bg-emerald-50 px-3 py-1 text-[11px] font-medium text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-900/30 dark:text-emerald-200">
                                Filtrando: SOLO nomenclaturas que inician con <span class="font-mono">NDESC-</span>
                            </div>
                        @endif
                    </div>

                    {{-- Botón Descargar --}}
                    <div class="mt-2">
                        <a href="{{ route('numericos.nomenclatura_efectiva.export') }}"
                           class="inline-flex items-center gap-2 rounded-lg border border-emerald-300/60 bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-700 hover:bg-emerald-100 dark:border-emerald-500/40 dark:bg-emerald-900/30 dark:text-emerald-200">
                            Descargar Excel
                        </a>
                    </div>
                </div>
            </div>

            {{-- KPIs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Total filas con nomenclatura</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalFilas) }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Zonas únicas</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($zonasUnicas) }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Subzonas únicas</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($subzonasUnicas) }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Distritos únicos</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($distritosUnicos) }}</p>
                </div>
            </div>

            {{-- Tablas: Zonas / Subzonas / Distritos --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                {{-- ZONAS (todas) --}}
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Zonas (todas)</h2>
                    </div>
                    <div class="p-4 overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="text-left text-gray-700 dark:text-gray-200">
                            <tr><th class="py-2 pr-3">Zona</th><th class="py-2 text-right">Cantidad</th></tr>
                            </thead>
                            <tbody class="text-gray-800 dark:text-gray-100">
                            @forelse ($porZonas as $z)
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td class="py-1.5 pr-3">{{ $z->zona ?? '—' }}</td>
                                    <td class="py-1.5 text-right font-medium">{{ number_format($z->cantidad) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="py-3 text-center text-gray-500 dark:text-gray-400">Sin datos</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SUBZONAS (con SZ-) --}}
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Subzonas (con SZ-)</h2>
                    </div>
                    <div class="p-4 overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="text-left text-gray-700 dark:text-gray-200">
                            <tr><th class="py-2 pr-3">Subzona</th><th class="py-2 text-right">Cantidad</th></tr>
                            </thead>
                            <tbody class="text-gray-800 dark:text-gray-100">
                            @forelse ($porSubzonas as $s)
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td class="py-1.5 pr-3">{{ $s->subzona }}</td>
                                    <td class="py-1.5 text-right font-medium">{{ number_format($s->cantidad) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="py-3 text-center text-gray-500 dark:text-gray-400">Sin datos</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- DISTRITOS (con D-) --}}
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Distritos (con D-)</h2>
                    </div>
                    <div class="p-4 overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead class="text-left text-gray-700 dark:text-gray-200">
                            <tr><th class="py-2 pr-3">Distrito</th><th class="py-2 text-right">Cantidad</th></tr>
                            </thead>
                            <tbody class="text-gray-800 dark:text-gray-100">
                            @forelse ($porDistritos as $d)
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td class="py-1.5 pr-3">{{ $d->distrito }}</td>
                                    <td class="py-1.5 text-right font-medium">{{ number_format($d->cantidad) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="py-3 text-center text-gray-500 dark:text-gray-400">Sin datos</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ZONAS (solo filas SIN SZ-) --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 mb-8">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Zonas (solo filas sin SZ-)</h2>
                    <span class="text-[10px] text-gray-500 dark:text-gray-400">Para cadenas sin -SZ-, solo se cuenta la zona (NDESC-Zn).</span>
                </div>
                <div class="p-4 overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="text-left text-gray-700 dark:text-gray-200">
                        <tr><th class="py-2 pr-3">Zona</th><th class="py-2 text-right">Cantidad</th></tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-100">
                        @forelse ($porZonasSoloSinSZ as $z)
                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                <td class="py-1.5 pr-3">{{ $z->zona ?? '—' }}</td>
                                <td class="py-1.5 text-right font-medium">{{ number_format($z->cantidad) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="py-3 text-center text-gray-500 dark:text-gray-400">Sin datos</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- CONSOLIDADO (Zona / Subzona / Distrito + Sup/Sub/Clases-Policías) --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                        Consolidado (Zona / Subzona / Distrito)
                    </h2>
                    <span class="text-[10px] text-gray-500 dark:text-gray-400">
          Clasificación por <span class="font-mono">cuadro_policial</span>.
        </span>
                </div>
                <div class="p-4 overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="text-left text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="py-2 pr-3">Zona</th>
                            <th class="py-2 pr-3">Subzona</th>
                            <th class="py-2 pr-3">Distrito</th>
                            <th class="py-2 text-right">Total</th>
                            <th class="py-2 text-right">Of. Superiores</th>
                            <th class="py-2 text-right">Of. Subalternos</th>
                            <th class="py-2 text-right">Clases y Policías</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-100">
                        @forelse ($consolidadoCp as $row)
                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                <td class="py-1.5 pr-3">{{ $row->zona ?? '—' }}</td>
                                <td class="py-1.5 pr-3">{{ $row->subzona ?? '' }}</td>
                                <td class="py-1.5 pr-3">{{ $row->distrito ?? '' }}</td>
                                <td class="py-1.5 text-right font-medium">{{ number_format($row->cantidad) }}</td>
                                <td class="py-1.5 text-right">{{ number_format($row->sup ?? 0) }}</td>
                                <td class="py-1.5 text-right">{{ number_format($row->sub ?? 0) }}</td>
                                <td class="py-1.5 text-right">{{ number_format($row->clpol ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-3 text-center text-gray-500 dark:text-gray-400">Sin datos</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
@endsection
