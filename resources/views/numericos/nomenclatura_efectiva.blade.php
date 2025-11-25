{{-- resources/views/numericos/nomenclatura_efectiva.blade.php --}}
@extends('layouts.app')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp
    <section class="bg-gray-50 dark:bg-gray-900 py-6">
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-8">

            {{-- Encabezado + Botón Descargar --}}
            <div class="mb-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                            Numéricos por Nomenclatura Efectiva
                        </h1>
                        <p class="text-xs text-gray-600 dark:text-gray-300">
                            Fuente: <span class="font-mono">usuarios.nomenclatura_efectiva</span>
                        </p>

                        @if(!empty($soloNdesc))
                            <div class="mt-3 inline-flex items-center gap-2 rounded-full border border-emerald-300/60 bg-emerald-50 px-3 py-1 text-[11px] font-medium text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-900/30 dark:text-emerald-200">
                                Filtrando principalmente cadenas que inician con <span class="font-mono">NDESC-</span>
                            </div>
                        @endif

                        @if(!empty($gradosSeleccionados))
                            <div class="mt-2 flex flex-wrap gap-1">
                                <span class="text-[10px] text-gray-500 dark:text-gray-400 mr-1">Grados seleccionados:</span>
                                @foreach($gradosSeleccionados as $g)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200 border border-emerald-300/60">
                                    {{ $g }}
                                </span>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-2 text-[10px] text-gray-500 dark:text-gray-400">
                                Sin filtro de grados: se consideran todos los grados.
                            </div>
                        @endif
                    </div>

                    {{-- Botón Descargar (respeta filtro por grados) --}}
                    <div class="mt-1">
                        <a
                            href="{{ route('numericos.nomenclatura_efectiva.export', ['grados' => $gradosSeleccionados ?? []]) }}"
                            class="inline-flex items-center gap-2 rounded-lg border border-emerald-300/60 bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-700 hover:bg-emerald-100 dark:border-emerald-500/40 dark:bg-emerald-900/30 dark:text-emerald-100"
                        >
                            Descargar Excel
                        </a>
                    </div>
                </div>
            </div>

            {{-- FILTRO POR GRADOS (columna grado) --}}
            <div class="mb-6 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                <form method="GET" action="{{ route('numericos.nomenclatura_efectiva.index') }}" class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            Filtrar por grado (columna: <span class="font-mono">grado</span>)
                        </h2>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400">
                        Selecciona uno o varios grados; todos los numéricos (NDESC, Tránsito, Cárceles, Unidades) se recalculan solo con esos grados.
                    </span>
                    </div>

                    @if(!empty($gradosDisponibles))
                        <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto">
                            @foreach($gradosDisponibles as $grado)
                                <label class="inline-flex items-center space-x-2 text-[11px] px-2 py-1 rounded border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40">
                                    <input
                                        type="checkbox"
                                        name="grados[]"
                                        value="{{ $grado }}"
                                        class="h-3 w-3 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500"
                                        {{ in_array($grado, $gradosSeleccionados ?? []) ? 'checked' : '' }}
                                    >
                                    <span class="font-mono text-gray-700 dark:text-gray-200">{{ $grado }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                            No se encontraron grados en la tabla <span class="font-mono">usuarios</span>.
                        </p>
                    @endif

                    <div class="flex items-center justify-between pt-2">
                        <div class="text-[10px] text-gray-500 dark:text-gray-400">
                            Puedes combinar varios grados a la vez.
                        </div>
                        <div class="flex gap-2">
                            <a
                                href="{{ route('numericos.nomenclatura_efectiva.index') }}"
                                class="inline-flex items-center rounded-lg border border-gray-300 px-2.5 py-1.5 text-[11px] text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Limpiar filtros
                            </a>
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-1.5 text-[11px] font-medium text-white hover:bg-emerald-700"
                            >
                                Aplicar filtro
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- TABS PRINCIPALES: NDESC / TRÁNSITO / CÁRCELES / UNIDADES --}}
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex flex-wrap gap-2 text-xs font-medium">
                    @php
                        $mainTabs = ['ndesc' => 'NDESC', 'transito' => 'Tránsito', 'carceles' => 'Cárceles (CCP / CPL)', 'unidades' => 'Unidades (resto nomenclaturas)'];
                    @endphp
                    @foreach($mainTabs as $key => $label)
                        <button
                            type="button"
                            data-main-tab="{{ $key }}"
                            class="main-tab-btn inline-flex items-center border-b-2 border-transparent px-3 py-2 text-[11px] text-gray-500 hover:text-emerald-700 hover:border-emerald-400 dark:text-gray-300"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- CONTENIDO DE TABS PRINCIPALES --}}
            {{-- =============== TAB NDESC =============== --}}
            <div id="main-tab-ndesc" class="main-tab-content">
                {{-- Sub-tabs NDESC: Tabla / Gráfico --}}
                <div class="mb-3 border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex gap-2 text-xs">
                        <button type="button" data-sub-tab="ndesc-tabla"
                                class="sub-tab-btn inline-flex items-center border-b-2 border-emerald-500 px-3 py-2 text-[11px] text-emerald-700 dark:text-emerald-300">
                            Tabla
                        </button>
                        <button type="button" data-sub-tab="ndesc-grafico"
                                class="sub-tab-btn inline-flex items-center border-b-2 border-transparent px-3 py-2 text-[11px] text-gray-500 hover:text-emerald-700 dark:text-gray-300">
                            Gráfico estados efectivos
                        </button>
                    </nav>
                </div>

                {{-- NDESC: TABLA --}}
                <div id="sub-tab-ndesc-tabla" class="sub-tab-content">
                    {{-- KPIs --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">Total filas (NDESC)</p>
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
                                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">NDESC - Zonas (todas)</h2>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="min-w-full text-xs">
                                    <thead class="text-left text-gray-700 dark:text-gray-200">
                                    <tr>
                                        <th class="py-2 pr-3">Zona</th>
                                        <th class="py-2 text-right">Cantidad</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-gray-800 dark:text-gray-100">
                                    @forelse ($porZonas as $z)
                                        <tr class="border-t border-gray-100 dark:border-gray-700">
                                            <td class="py-1.5 pr-3">{{ $z->zona ?? '—' }}</td>
                                            <td class="py-1.5 text-right font-medium">{{ number_format($z->cantidad) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-3 text-center text-gray-500 dark:text-gray-400">Sin datos</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- SUBZONAS (con SZ-) --}}
                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">NDESC - Subzonas (con SZ-)</h2>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="min-w-full text-xs">
                                    <thead class="text-left text-gray-700 dark:text-gray-200">
                                    <tr>
                                        <th class="py-2 pr-3">Subzona</th>
                                        <th class="py-2 text-right">Cantidad</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-gray-800 dark:text-gray-100">
                                    @forelse ($porSubzonas as $s)
                                        <tr class="border-t border-gray-100 dark:border-gray-700">
                                            <td class="py-1.5 pr-3">{{ $s->subzona }}</td>
                                            <td class="py-1.5 text-right font-medium">{{ number_format($s->cantidad) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-3 text-center text-gray-500 dark:text-gray-400">Sin datos</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- DISTRITOS (con D-) --}}
                        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">NDESC - Distritos (con D-)</h2>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="min-w-full text-xs">
                                    <thead class="text-left text-gray-700 dark:text-gray-200">
                                    <tr>
                                        <th class="py-2 pr-3">Distrito</th>
                                        <th class="py-2 text-right">Cantidad</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-gray-800 dark:text-gray-100">
                                    @forelse ($porDistritos as $d)
                                        <tr class="border-t border-gray-100 dark:border-gray-700">
                                            <td class="py-1.5 pr-3">{{ $d->distrito }}</td>
                                            <td class="py-1.5 text-right font-medium">{{ number_format($d->cantidad) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-3 text-center text-gray-500 dark:text-gray-400">Sin datos</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    {{-- ZONAS (solo filas SIN SZ-) --}}
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 mb-8">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">NDESC - Zonas (solo filas sin SZ-)</h2>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">Para cadenas sin -SZ-, solo se cuenta la zona (NDESC-Zn).</span>
                        </div>
                        <div class="p-4 overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead class="text-left text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="py-2 pr-3">Zona</th>
                                    <th class="py-2 text-right">Cantidad</th>
                                </tr>
                                </thead>
                                <tbody class="text-gray-800 dark:text-gray-100">
                                @forelse ($porZonasSoloSinSZ as $z)
                                    <tr class="border-t border-gray-100 dark:border-gray-700">
                                        <td class="py-1.5 pr-3">{{ $z->zona ?? '—' }}</td>
                                        <td class="py-1.5 text-right font-medium">{{ number_format($z->cantidad) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-3 text-center text-gray-500 dark:text-gray-400">Sin datos</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- CONSOLIDADO (Zona / Subzona / Distrito + Sup/Sub/Clases-Policías) --}}
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                NDESC - Consolidado (Zona / Subzona / Distrito)
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
                                    <tr>
                                        <td colspan="7" class="py-3 text-center text-gray-500 dark:text-gray-400">Sin datos</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- NDESC: GRÁFICO ESTADOS --}}
                <div id="sub-tab-ndesc-grafico" class="sub-tab-content hidden">
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                Distribución por estado_efectivo (NDESC)
                            </h3>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                            Fuente: <span class="font-mono">usuarios.estado_efectivo</span>
                        </span>
                        </div>
                        <div class="h-72">
                            <canvas id="chart-ndesc-estados"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =============== TAB TRÁNSITO =============== --}}
            <div id="main-tab-transito" class="main-tab-content hidden">
                <div class="mb-3 border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex gap-2 text-xs">
                        <button type="button" data-sub-tab="transito-tabla"
                                class="sub-tab-btn inline-flex items-center border-b-2 border-emerald-500 px-3 py-2 text-[11px] text-emerald-700 dark:text-emerald-300">
                            Tabla
                        </button>
                        <button type="button" data-sub-tab="transito-grafico"
                                class="sub-tab-btn inline-flex items-center border-b-2 border-transparent px-3 py-2 text-[11px] text-gray-500 hover:text-emerald-700 dark:text-gray-300">
                            Gráfico estados efectivos
                        </button>
                    </nav>
                </div>

                {{-- TRÁNSITO TABLA --}}
                <div id="sub-tab-transito-tabla" class="sub-tab-content">
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                Tránsito - NDESC-Z4-SZ-STO DOMINGO TSACHILAS-JPREV-CTSV-...
                            </h2>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                            Registros que comienzan con ese tronco, filtrados por grado seleccionado.
                        </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead class="text-left text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="py-2 pr-3">Zona</th>
                                    <th class="py-2 pr-3">Subzona</th>
                                    <th class="py-2 pr-3">Distrito</th>
                                    <th class="py-2 text-right">Total</th>
                                </tr>
                                </thead>
                                <tbody class="text-gray-800 dark:text-gray-100">
                                @forelse($transitoTabla as $t)
                                    <tr class="border-t border-gray-100 dark:border-gray-700">
                                        <td class="py-1.5 pr-3">{{ $t->zona ?? '—' }}</td>
                                        <td class="py-1.5 pr-3">{{ $t->subzona ?? '' }}</td>
                                        <td class="py-1.5 pr-3">{{ $t->distrito ?? '' }}</td>
                                        <td class="py-1.5 text-right font-medium">{{ number_format($t->total) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-3 text-center text-gray-500 dark:text-gray-400">
                                            Sin datos para Tránsito con los filtros actuales.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TRÁNSITO GRÁFICO --}}
                <div id="sub-tab-transito-grafico" class="sub-tab-content hidden">
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                Distribución por estado_efectivo (Tránsito)
                            </h3>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                            Solo registros de Tránsito (CTSV) según filtro de grados.
                        </span>
                        </div>
                        <div class="h-72">
                            <canvas id="chart-transito-estados"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =============== TAB CÁRCELES =============== --}}
            <div id="main-tab-carceles" class="main-tab-content hidden">
                <div class="mb-3 border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex gap-2 text-xs">
                        <button type="button" data-sub-tab="carceles-tabla"
                                class="sub-tab-btn inline-flex items-center border-b-2 border-emerald-500 px-3 py-2 text-[11px] text-emerald-700 dark:text-emerald-300">
                            Tabla
                        </button>
                        <button type="button" data-sub-tab="carceles-grafico"
                                class="sub-tab-btn inline-flex items-center border-b-2 border-transparent px-3 py-2 text-[11px] text-gray-500 hover:text-emerald-700 dark:text-gray-300">
                            Gráfico estados efectivos
                        </button>
                    </nav>
                </div>

                {{-- CÁRCELES TABLA --}}
                <div id="sub-tab-carceles-tabla" class="sub-tab-content">
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                Servidores en Centros de Privación de Libertad (CCP / CPL)
                            </h2>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                            Desglose por Nomenclatura y por Grado (según filtro).
                        </span>
                        </div>

                        @php
                            $carcelesGrados = collect($carcelesTabla ?? [])->flatMap(function($row){
                                return array_keys($row['por_grado'] ?? []);
                            })->unique()->values();
                        @endphp

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-[11px]">
                                <thead class="text-left text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="py-2 pr-3">Zona</th>
                                    <th class="py-2 pr-3">Subzona</th>
                                    <th class="py-2 pr-3">Distrito</th>
                                    <th class="py-2 pr-3">Nomenclatura</th>
                                    <th class="py-2 text-right">Total</th>
                                    @foreach($carcelesGrados as $g)
                                        <th class="py-2 text-right">{{ $g }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody class="text-gray-800 dark:text-gray-100">
                                @forelse($carcelesTabla as $row)
                                    <tr class="border-t border-gray-100 dark:border-gray-700">
                                        <td class="py-1.5 pr-3">{{ $row['zona'] ?? '—' }}</td>
                                        <td class="py-1.5 pr-3">{{ $row['subzona'] ?? '' }}</td>
                                        <td class="py-1.5 pr-3">{{ $row['distrito'] ?? '' }}</td>
                                        <td class="py-1.5 pr-3">{{ $row['nomenclatura'] }}</td>
                                        <td class="py-1.5 text-right font-medium">{{ number_format($row['total']) }}</td>
                                        @foreach($carcelesGrados as $g)
                                            <td class="py-1.5 text-right">
                                                {{ number_format($row['por_grado'][$g] ?? 0) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 5 + $carcelesGrados->count() }}" class="py-3 text-center text-gray-500 dark:text-gray-400">
                                            No hay registros CCP / CPL con los filtros actuales.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- CÁRCELES GRÁFICO --}}
                <div id="sub-tab-carceles-grafico" class="sub-tab-content hidden">
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                Distribución por estado_efectivo (Cárceles CCP / CPL)
                            </h3>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                            Solo nomenclaturas que contienen CCP o CPL.
                        </span>
                        </div>
                        <div class="h-72">
                            <canvas id="chart-carceles-estados"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =============== TAB UNIDADES =============== --}}
            <div id="main-tab-unidades" class="main-tab-content hidden">
                <div class="mb-3 border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex gap-2 text-xs">
                        <button type="button" data-sub-tab="unidades-tabla"
                                class="sub-tab-btn inline-flex items-center border-b-2 border-emerald-500 px-3 py-2 text-[11px] text-emerald-700 dark:text-emerald-300">
                            Tabla
                        </button>
                        <button type="button" data-sub-tab="unidades-grafico"
                                class="sub-tab-btn inline-flex items-center border-b-2 border-transparent px-3 py-2 text-[11px] text-gray-500 hover:text-emerald-700 dark:text-gray-300">
                            Gráfico estados efectivos
                        </button>
                    </nav>
                </div>

                {{-- UNIDADES TABLA --}}
                <div id="sub-tab-unidades-tabla" class="sub-tab-content">
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                Unidades (resto de nomenclaturas)
                            </h2>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                            No incluyen NDESC, ni nomenclaturas con CCP / CPL.
                        </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-[11px]">
                                <thead class="text-left text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="py-2 pr-3">Tronco</th>
                                    <th class="py-2 text-right">Aprobado</th>
                                    <th class="py-2 text-right">Efectivo</th>
                                    <th class="py-2 text-right">Diferencia</th>
                                    <th class="py-2 text-center">Detalle</th>
                                </tr>
                                </thead>
                                <tbody class="text-gray-800 dark:text-gray-100">
                                @forelse($otrasTabla as $u)
                                    @php
                                        $troncoKey = $u->tronco ?? '(SIN TRONCO)';
                                        $slug = Str::slug($troncoKey, '_');
                                    @endphp
                                    <tr class="border-t border-gray-100 dark:border-gray-700">
                                        <td class="py-1.5 pr-3">
                                            <span class="font-mono">{{ $troncoKey }}</span>
                                        </td>
                                        <td class="py-1.5 text-right">
                                            {{ number_format($u->aprobado ?? 0) }}
                                        </td>
                                        <td class="py-1.5 text-right">
                                            {{ number_format($u->seleccionados ?? 0) }}
                                        </td>
                                        <td class="py-1.5 text-right">
                                            {{ number_format($u->diferencia ?? 0) }}
                                        </td>
                                        <td class="py-1.5 text-center">
                                            @if(!empty($otrasDetalle[$troncoKey]))
                                                <button
                                                    type="button"
                                                    onclick="openDetalleModal('modal-unidad-{{ $slug }}')"
                                                    class="inline-flex items-center rounded-md bg-emerald-600 px-2.5 py-1 text-[11px] font-medium text-white hover:bg-emerald-700"
                                                >
                                                    Ver detalle
                                                </button>
                                            @else
                                                <span class="text-[10px] text-gray-400">Sin detalle</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-3 text-center text-gray-500 dark:text-gray-400">
                                            No hay unidades registradas con los filtros actuales.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- MODALES DE DETALLE POR TRONCO --}}
                    @if(!empty($otrasDetalle))
                        @foreach($otrasDetalle as $tronco => $listaDetalle)
                            @php
                                $troncoKey = $tronco ?? '(SIN TRONCO)';
                                $slug = Str::slug($troncoKey, '_');
                            @endphp
                            <div
                                id="modal-unidad-{{ $slug }}"
                                class="fixed inset-0 z-40 hidden items-center justify-center bg-black/40 backdrop-blur-sm"
                                aria-hidden="true"
                            >
                                <div class="relative max-h-[80vh] w-full max-w-4xl overflow-hidden rounded-2xl bg-white dark:bg-gray-900 shadow-xl border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                            Detalle de nomenclaturas para tronco:
                                            <span class="font-mono text-emerald-600 dark:text-emerald-300">{{ $troncoKey }}</span>
                                        </h3>
                                        <button
                                            type="button"
                                            class="rounded-full p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800"
                                            onclick="closeDetalleModal('modal-unidad-{{ $slug }}')"
                                        >
                                            <span class="sr-only">Cerrar</span>
                                            ✕
                                        </button>
                                    </div>
                                    <div class="px-4 py-3 overflow-auto max-h-[70vh]">
                                        <table class="min-w-full text-[11px]">
                                            <thead class="text-left text-gray-700 dark:text-gray-200">
                                            <tr>
                                                <th class="py-2 pr-3">Nomenclatura</th>
                                                <th class="py-2 text-right">Aprobado</th>
                                                <th class="py-2 text-right">Efectivo</th>
                                                <th class="py-2 text-right">Diferencia</th>
                                                <th class="py-2 text-right">Unidad Origen</th>
                                                <th class="py-2 text-right">Tras. Temporal</th>
                                                <th class="py-2 text-right">Temp. por Excedente</th>
                                                <th class="py-2 text-right">Tras. Eventual</th>
                                                <th class="py-2 text-right">Otros</th>
                                            </tr>
                                            </thead>
                                            <tbody class="text-gray-800 dark:text-gray-100">
                                            @foreach($listaDetalle as $d)
                                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                                    <td class="py-1.5 pr-3">
                                                        <span class="font-mono">{{ $d->nomenclatura }}</span>
                                                    </td>
                                                    <td class="py-1.5 text-right">{{ number_format($d->aprobado ?? 0) }}</td>
                                                    <td class="py-1.5 text-right">{{ number_format($d->efectivo ?? 0) }}</td>
                                                    <td class="py-1.5 text-right">{{ number_format($d->diferencia ?? 0) }}</td>
                                                    <td class="py-1.5 text-right">{{ number_format($d->unidad_origen ?? 0) }}</td>
                                                    <td class="py-1.5 text-right">{{ number_format($d->traslado_temporal ?? 0) }}</td>
                                                    <td class="py-1.5 text-right">{{ number_format($d->traslado_excedente ?? 0) }}</td>
                                                    <td class="py-1.5 text-right">{{ number_format($d->traslado_eventual ?? 0) }}</td>
                                                    <td class="py-1.5 text-right">{{ number_format($d->otros ?? 0) }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 flex justify-end">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md bg-gray-100 px-3 py-1.5 text-[11px] text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                                            onclick="closeDetalleModal('modal-unidad-{{ $slug }}')"
                                        >
                                            Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- UNIDADES GRÁFICO --}}
                <div id="sub-tab-unidades-grafico" class="sub-tab-content hidden">
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                Distribución por estado_efectivo (Unidades)
                            </h3>
                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                            Considera todas las unidades que no son NDESC y no contienen CCP / CPL.
                        </span>
                        </div>
                        <div class="h-72">
                            <canvas id="chart-unidades-estados"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- JS: Tabs + Modales + Gráficos --}}
    @push('scripts')
        {{-- Chart.js CDN (si no lo tienes ya en tu layout) --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // ----------- Tabs principales -----------
            const mainTabButtons = document.querySelectorAll('.main-tab-btn');
            const mainTabContents = document.querySelectorAll('.main-tab-content');

            function activateMainTab(key) {
                mainTabButtons.forEach(btn => {
                    const tab = btn.getAttribute('data-main-tab');
                    if (tab === key) {
                        btn.classList.add('border-emerald-500', 'text-emerald-700', 'dark:text-emerald-300');
                        btn.classList.remove('border-transparent', 'text-gray-500');
                    } else {
                        btn.classList.remove('border-emerald-500', 'text-emerald-700', 'dark:text-emerald-300');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    }
                });
                mainTabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                const active = document.getElementById('main-tab-' + key);
                if (active) active.classList.remove('hidden');
            }

            mainTabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const key = btn.getAttribute('data-main-tab');
                    activateMainTab(key);
                });
            });

            // Activar por defecto NDESC
            activateMainTab('ndesc');

            // ----------- Sub-tabs -----------
            const subTabButtons = document.querySelectorAll('.sub-tab-btn');

            function activateSubTab(groupPrefix, subKey) {
                document.querySelectorAll('#main-tab-' + groupPrefix + ' .sub-tab-btn').forEach(btn => {
                    const target = btn.getAttribute('data-sub-tab');
                    if (target === subKey) {
                        btn.classList.add('border-emerald-500', 'text-emerald-700', 'dark:text-emerald-300');
                        btn.classList.remove('border-transparent', 'text-gray-500');
                    } else {
                        btn.classList.remove('border-emerald-500', 'text-emerald-700', 'dark:text-emerald-300');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    }
                });
                document.querySelectorAll('#main-tab-' + groupPrefix + ' .sub-tab-content').forEach(el => {
                    el.classList.add('hidden');
                });
                const content = document.getElementById('sub-tab-' + subKey);
                if (content) content.classList.remove('hidden');
            }

            subTabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const subKey = btn.getAttribute('data-sub-tab'); // ej: ndesc-tabla
                    if (!subKey) return;
                    const parts = subKey.split('-'); // [ndesc, tabla]
                    const groupPrefix = parts[0];    // ndesc
                    activateSubTab(groupPrefix, subKey);
                });
            });

            // Activar por defecto los "tabla"
            activateSubTab('ndesc', 'ndesc-tabla');
            activateSubTab('transito', 'transito-tabla');
            activateSubTab('carceles', 'carceles-tabla');
            activateSubTab('unidades', 'unidades-tabla');

            // ----------- Modales UNIDADES -----------
            function openDetalleModal(id) {
                const el = document.getElementById(id);
                if (el) el.classList.remove('hidden');
            }
            function closeDetalleModal(id) {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            }
            window.openDetalleModal = openDetalleModal;
            window.closeDetalleModal = closeDetalleModal;

            // ----------- Gráficos (Chart.js) -----------
            const ndescEstados = @json($ndescEstados ?? []);
            const transitoEstados = @json($transitoEstados ?? []);
            const carcelesEstados = @json($carcelesEstados ?? []);
            const otrasEstados = @json($otrasEstados ?? []);

            function buildPieChart(canvasId, data) {
                const el = document.getElementById(canvasId);
                if (!el || !data.length) return;

                const labels = data.map(d => d.estado_efectivo ?? 'SIN ESTADO');
                const values = data.map(d => Number(d.cantidad ?? 0));

                new Chart(el.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                        }]
                    },
                    options: {
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                buildPieChart('chart-ndesc-estados', ndescEstados);
                buildPieChart('chart-transito-estados', transitoEstados);
                buildPieChart('chart-carceles-estados', carcelesEstados);
                buildPieChart('chart-unidades-estados', otrasEstados);
            });
        </script>
    @endpush
@endsection
