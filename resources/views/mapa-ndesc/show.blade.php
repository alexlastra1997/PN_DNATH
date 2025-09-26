@extends('layouts.app')

@php
    use Illuminate\Support\Str;

    /** Normaliza como en el controlador: mayúsculas, sin acentos, espacios colapsados */
    function norm_txt($s) {
        $s = trim((string)$s);
        $s = mb_strtoupper($s, 'UTF-8');
        $s = strtr($s, ['Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U','Ñ'=>'N']);
        $s = preg_replace('/\s+/u', ' ', $s);
        return $s ?? '';
    }

    /** Cumple si el grado del usuario está entre los válidos para [distrito][cargo normalizado] */
    function cumple_grado($u, $distrito, $requisitosGrado) {
        $cargoKey = norm_txt($u->funcion_efectiva ?? '');
        $gradoU   = norm_txt($u->grado ?? '');
        if (!isset($requisitosGrado[$distrito][$cargoKey])) return true;
        $set = $requisitosGrado[$distrito][$cargoKey]; // [grado=>true]
        return array_key_exists($gradoU, $set);
    }
@endphp

@section('content')
    <div class="p-6 space-y-8">

        <div class="flex items-center justify-between">
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">
                NDESC · Z{{ $zona }} · SZ {{ $subzonaNombre }} — Comandantes, Subcomandantes, Jefes y Subjefes
            </h1>

            <div class="flex items-center gap-2">
                <a href="{{ route('mapa.ndesc.export', [$zona, Str::slug($subzonaNombre, '-')]) }}"
                   class="px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm">
                    Descargar Excel
                </a>
                <a href="{{ route('mapa.ndesc') }}"
                   class="text-sm text-emerald-700 dark:text-emerald-400 hover:underline">← Volver al mapa</a>
            </div>
        </div>

        @php
            $resumenSubzona = $estadoPorDistrito['SIN DISTRITO'] ?? [];
            $listaSubzona   = $usuariosPorDistrito['SIN DISTRITO'] ?? collect();

            // tabs por distrito (excluye SUBZONA)
            $dKeys = collect(array_keys($estadoPorDistrito))
                ->reject(fn($k) => $k === 'SIN DISTRITO')
                ->sort(fn($a,$b)=>strnatcasecmp($a,$b))
                ->values();
        @endphp

        {{-- ================== TABS PRINCIPALES ================== --}}
        <div class="border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center"
                id="tabs-ndesc"
                data-tabs-toggle="#tabs-ndesc-content"
                role="tablist">

                <li class="mr-2" role="presentation">
                    <button class="inline-block p-2 border-b-2 rounded-t-lg aria-selected:border-emerald-600 aria-selected:text-emerald-600"
                            id="tab-comandantes"
                            data-tabs-target="#panel-comandantes"
                            type="button" role="tab"
                            aria-controls="panel-comandantes" aria-selected="true">
                        Comandantes
                    </button>
                </li>

                <li class="mr-2" role="presentation">
                    <button class="inline-block p-2 border-b-2 rounded-t-lg aria-selected:border-emerald-600 aria-selected:text-emerald-600"
                            id="tab-subzona"
                            data-tabs-target="#panel-subzona"
                            type="button" role="tab"
                            aria-controls="panel-subzona" aria-selected="false">
                        Subzona
                    </button>
                </li>

                @foreach ($dKeys as $distrito)
                    @php
                        $did = 'tab-'.Str::slug($distrito, '-');
                        $pid = 'panel-'.Str::slug($distrito, '-');
                    @endphp
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-2 border-b-2 rounded-t-lg aria-selected:border-emerald-600 aria-selected:text-emerald-600"
                                id="{{ $did }}"
                                data-tabs-target="#{{ $pid }}"
                                type="button" role="tab"
                                aria-controls="{{ $pid }}" aria-selected="false">
                            {{ $distrito }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div id="tabs-ndesc-content">
            {{-- ===== Comandantes ===== --}}
            <div id="panel-comandantes" role="tabpanel" aria-labelledby="tab-comandantes">
                @php $dist = 'SIN DISTRITO'; @endphp
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-x-auto mt-4">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">BAS</th>
                            <th class="px-3 py-2 text-left">Cédula</th>
                            <th class="px-3 py-2 text-left">Apellidos y Nombres</th>
                            <th class="px-3 py-2 text-left">Grado</th>
                            <th class="px-3 py-2 text-left">Promoción</th>
                            <th class="px-3 py-2 text-left">Función Efectiva</th>
                            <th class="px-3 py-2 text-left">Fecha Efectiva</th>
                            <th class="px-3 py-2 text-left">Tipo de traslado</th>
                            <th class="px-3 py-2 text-left">Observaciones</th>
                            <th class="px-3 py-2 text-left">Nomenclatura Efectiva</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                        @forelse ($leadersSubzona as $u)
                            @php
                                $ok = cumple_grado($u, $dist, $requisitosGrado);
                                $cargoKey   = norm_txt($u->funcion_efectiva ?? '');
                                $validosSet = $requisitosGrado[$dist][$cargoKey] ?? [];
                                $validos    = implode(', ', array_keys($validosSet));
                            @endphp
                            <tr class="border-t border-gray-200 dark:border-gray-800 odd:bg-gray-50 dark:odd:bg-gray-900 even:bg-white dark:even:bg-gray-950">
                                <td class="px-3 py-2">
                                    <button type="button"
                                            class="btn-bas px-2 py-1 text-xs rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white"
                                            data-modal-target="basModal" data-modal-toggle="basModal"
                                            data-cedula="{{ $u->cedula }}"
                                            data-nombre="{{ $u->apellidos_nombres }}"
                                            data-grado="{{ $u->grado }}"
                                            data-promocion="{{ $u->promocion }}"
                                            data-funcion="{{ $u->funcion_efectiva }}"
                                            data-fecha="{{ $u->fecha_efectiva }}"
                                            data-traslado="{{ $u->estado_efectivo ?? '-' }}"
                                            data-nomen="{{ $u->nomenclatura_efectiva }}"
                                            data-distrito="{{ $dist }}"
                                            data-alerta="{{ $ok ? '0' : '1' }}"
                                            data-validos="{{ $validos }}">Ver</button>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $u->cedula }}</td>
                                <td class="px-3 py-2">{{ $u->apellidos_nombres }}</td>
                                <td class="px-3 py-2">{{ $u->grado }}</td>
                                <td class="px-3 py-2">{{ $u->promocion }}</td>
                                <td class="px-3 py-2 font-medium">{{ $u->funcion_efectiva }}</td>
                                <td class="px-3 py-2">{{ $u->fecha_efectiva }}</td>
                                <td class="px-3 py-2">{{ $u->estado_efectivo ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    @unless ($ok)
                                        <span class="inline-flex items-center text-red-600 dark:text-red-400"
                                              title="Grado no coincide. Válidos: {{ $validos ?: 'N/D' }}">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.721-1.36 3.486 0l6.518 11.596c.75 1.335-.213 2.998-1.743 2.998H3.482c-1.53 0-2.492-1.663-1.743-2.998L8.257 3.1zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V8a1 1 0 112 0v3a1 1 0 01-1 1z" clip-rule="evenodd"/></svg>
                                    </span>
                                    @endunless
                                </td>
                                <td class="px-3 py-2">{{ $u->nomenclatura_efectiva }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="px-3 py-4 text-center text-gray-600 dark:text-gray-400">No se registran COMANDANTE/SUBCOMANDANTE SUBZONAL DE POLICÍA.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ===== Subzona (Resumen + Personal) ===== --}}
            <div class="hidden" id="panel-subzona" role="tabpanel" aria-labelledby="tab-subzona">
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-x-auto mt-4">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Cargo</th>
                            <th class="px-3 py-2 text-left">Ideal</th>
                            <th class="px-3 py-2 text-left">Actual</th>
                            <th class="px-3 py-2 text-left">Estado</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                        @forelse ($resumenSubzona as $r)
                            @php
                                $cl = match($r['estado']) {
                                    'EXCEDIDO' => 'text-red-600 dark:text-red-400 font-semibold',
                                    'COMPLETO' => 'text-blue-700 dark:text-blue-400 font-semibold',
                                    default    => 'text-yellow-700 dark:text-yellow-400 font-semibold'
                                };
                            @endphp
                            <tr class="border-t border-gray-200 dark:border-gray-800 odd:bg-gray-50 dark:odd:bg-gray-900 even:bg-white dark:even:bg-gray-950">
                                <td class="px-3 py-2">{{ $r['cargo'] }}</td>
                                <td class="px-3 py-2">{{ $r['ideal'] }}</td>
                                <td class="px-3 py-2">{{ $r['actual'] }}</td>
                                <td class="px-3 py-2 {{ $cl }}">{{ $r['estado'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-4 text-center text-gray-600 dark:text-gray-400">Sin datos de orgánico.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @php $dist = 'SIN DISTRITO'; @endphp
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-x-auto mt-6">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">BAS</th>
                            <th class="px-3 py-2 text-left">Cédula</th>
                            <th class="px-3 py-2 text-left">Apellidos y Nombres</th>
                            <th class="px-3 py-2 text-left">Grado</th>
                            <th class="px-3 py-2 text-left">Promoción</th>
                            <th class="px-3 py-2 text-left">Función Efectiva</th>
                            <th class="px-3 py-2 text-left">Fecha Efectiva</th>
                            <th class="px-3 py-2 text-left">Tipo de traslado</th>
                            <th class="px-3 py-2 text-left">Observaciones</th>
                            <th class="px-3 py-2 text-left">Nomenclatura Efectiva</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-800 dark:text-gray-200">
                        @forelse ($listaSubzona as $u)
                            @php
                                $ok = cumple_grado($u, $dist, $requisitosGrado);
                                $cargoKey   = norm_txt($u->funcion_efectiva ?? '');
                                $validosSet = $requisitosGrado[$dist][$cargoKey] ?? [];
                                $validos    = implode(', ', array_keys($validosSet));
                            @endphp
                            <tr class="border-t dark:border-gray-800 odd:bg-gray-50 dark:odd:bg-gray-900 even:bg-white dark:even:bg-gray-950">
                                <td class="px-3 py-2">
                                    <button type="button"
                                            class="btn-bas px-2 py-1 text-xs rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white"
                                            data-modal-target="basModal" data-modal-toggle="basModal"
                                            data-cedula="{{ $u->cedula }}"
                                            data-nombre="{{ $u->apellidos_nombres }}"
                                            data-grado="{{ $u->grado }}"
                                            data-promocion="{{ $u->promocion }}"
                                            data-funcion="{{ $u->funcion_efectiva }}"
                                            data-fecha="{{ $u->fecha_efectiva }}"
                                            data-traslado="{{ $u->estado_efectivo ?? '-' }}"
                                            data-nomen="{{ $u->nomenclatura_efectiva }}"
                                            data-distrito="{{ $dist }}"
                                            data-alerta="{{ $ok ? '0' : '1' }}"
                                            data-validos="{{ $validos }}">Ver</button>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $u->cedula }}</td>
                                <td class="px-3 py-2">{{ $u->apellidos_nombres }}</td>
                                <td class="px-3 py-2">{{ $u->grado }}</td>
                                <td class="px-3 py-2">{{ $u->promocion }}</td>
                                <td class="px-3 py-2 font-medium">{{ $u->funcion_efectiva }}</td>
                                <td class="px-3 py-2">{{ $u->fecha_efectiva }}</td>
                                <td class="px-3 py-2">{{ $u->estado_efectivo ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    @unless ($ok)
                                        <span class="inline-flex items-center text-red-600 dark:text-red-400"
                                              title="Grado no coincide. Válidos: {{ $validos ?: 'N/D' }}">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.721-1.36 3.486 0l6.518 11.596c.75 1.335-.213 2.998-1.743 2.998H3.482c-1.53 0-2.492-1.663-1.743-2.998L8.257 3.1zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V8a1 1 0 112 0v3a1 1 0 01-1 1z" clip-rule="evenodd"/></svg>
                                    </span>
                                    @endunless
                                </td>
                                <td class="px-3 py-2">{{ $u->nomenclatura_efectiva }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="px-3 py-4 text-center text-gray-600 dark:text-gray-400">No hay personal listado para la subzona.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ===== Distritos (cada uno en su tab) ===== --}}
            @foreach ($dKeys as $distrito)
                @php
                    $pid   = 'panel-'.Str::slug($distrito, '-');
                    $lista = $usuariosPorDistrito[$distrito] ?? collect();
                    $filas = $estadoPorDistrito[$distrito] ?? [];
                @endphp
                <div class="hidden" id="{{ $pid }}" role="tabpanel" aria-labelledby="tab-{{ Str::slug($distrito, '-') }}">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-x-auto mt-4">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Cargo</th>
                                <th class="px-3 py-2 text-left">Ideal</th>
                                <th class="px-3 py-2 text-left">Actual</th>
                                <th class="px-3 py-2 text-left">Estado</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-800 dark:text-gray-200">
                            @foreach ($filas as $r)
                                @php
                                    $cl = match($r['estado']) {
                                        'EXCEDIDO' => 'text-red-600 dark:text-red-400 font-semibold',
                                        'COMPLETO' => 'text-blue-700 dark:text-blue-400 font-semibold',
                                        default    => 'text-yellow-700 dark:text-yellow-400 font-semibold'
                                    };
                                @endphp
                                <tr class="border-t border-gray-200 dark:border-gray-800 odd:bg-gray-50 dark:odd:bg-gray-900 even:bg-white dark:even:bg-gray-950">
                                    <td class="px-3 py-2">{{ $r['cargo'] }}</td>
                                    <td class="px-3 py-2">{{ $r['ideal'] }}</td>
                                    <td class="px-3 py-2">{{ $r['actual'] }}</td>
                                    <td class="px-3 py-2 {{ $cl }}">{{ $r['estado'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-x-auto mt-6">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">BAS</th>
                                <th class="px-3 py-2 text-left">Cédula</th>
                                <th class="px-3 py-2 text-left">Apellidos y Nombres</th>
                                <th class="px-3 py-2 text-left">Grado</th>
                                <th class="px-3 py-2 text-left">Promoción</th>
                                <th class="px-3 py-2 text-left">Función Efectiva</th>
                                <th class="px-3 py-2 text-left">Fecha Efectiva</th>
                                <th class="px-3 py-2 text-left">Tipo de traslado</th>
                                <th class="px-3 py-2 text-left">Observaciones</th>
                                <th class="px-3 py-2 text-left">Nomenclatura Efectiva</th>
                            </tr>
                            </thead>
                            <tbody class="text-gray-800 dark:text-gray-200">
                            @forelse ($lista as $u)
                                @php
                                    $ok = cumple_grado($u, $distrito, $requisitosGrado);
                                    $cargoKey   = norm_txt($u->funcion_efectiva ?? '');
                                    $validosSet = $requisitosGrado[$distrito][$cargoKey] ?? [];
                                    $validos    = implode(', ', array_keys($validosSet));
                                @endphp
                                <tr class="border-t dark:border-gray-800 odd:bg-gray-50 dark:odd:bg-gray-900 even:bg-white dark:even:bg-gray-950">
                                    <td class="px-3 py-2">
                                        <button type="button"
                                                class="btn-bas px-2 py-1 text-xs rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white"
                                                data-modal-target="basModal" data-modal-toggle="basModal"
                                                data-cedula="{{ $u->cedula }}"
                                                data-nombre="{{ $u->apellidos_nombres }}"
                                                data-grado="{{ $u->grado }}"
                                                data-promocion="{{ $u->promocion }}"
                                                data-funcion="{{ $u->funcion_efectiva }}"
                                                data-fecha="{{ $u->fecha_efectiva }}"
                                                data-traslado="{{ $u->estado_efectivo ?? '-' }}"
                                                data-nomen="{{ $u->nomenclatura_efectiva }}"
                                                data-distrito="{{ $distrito }}"
                                                data-alerta="{{ $ok ? '0' : '1' }}"
                                                data-validos="{{ $validos }}">Ver</button>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">{{ $u->cedula }}</td>
                                    <td class="px-3 py-2">{{ $u->apellidos_nombres }}</td>
                                    <td class="px-3 py-2">{{ $u->grado }}</td>
                                    <td class="px-3 py-2">{{ $u->promocion }}</td>
                                    <td class="px-3 py-2 font-medium">{{ $u->funcion_efectiva }}</td>
                                    <td class="px-3 py-2">{{ $u->fecha_efectiva }}</td>
                                    <td class="px-3 py-2">{{ $u->estado_efectivo ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        @unless ($ok)
                                            <span class="inline-flex items-center text-red-600 dark:text-red-400"
                                                  title="Grado no coincide. Válidos: {{ $validos ?: 'N/D' }}">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.721-1.36 3.486 0l6.518 11.596c.75 1.335-.213 2.998-1.743 2.998H3.482c-1.53 0-2.492-1.663-1.743-2.998L8.257 3.1zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V8a1 1 0 112 0v3a1 1 0 01-1 1z" clip-rule="evenodd"/></svg>
                                        </span>
                                        @endunless
                                    </td>
                                    <td class="px-3 py-2">{{ $u->nomenclatura_efectiva }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="px-3 py-4 text-center text-gray-600 dark:text-gray-400">No hay personal para este distrito.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ================== MODAL BAS ================== --}}
    <div id="basModalBackdrop" class="hidden fixed inset-0 bg-black/60 z-[49]"></div>

    <div id="basModal" tabindex="-1" aria-hidden="true"
         class="hidden fixed inset-0 z-50 justify-center items-center w-full h-full overflow-y-auto overflow-x-hidden">
        <div class="relative p-4 w-full max-w-xl mx-auto">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Ficha del Servidor Policial</h3>
                    <button type="button" class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-700 dark:hover:text-white" data-modal-hide="basModal">
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                        <span class="sr-only">Cerrar</span>
                    </button>
                </div>
                <div class="p-4 space-y-3 text-sm text-gray-800 dark:text-gray-200">
                    <div class="grid grid-cols-2 gap-3">
                        <div><span class="font-medium text-gray-600 dark:text-gray-400">Cédula:</span> <span id="bas-cedula"></span></div>
                        <div><span class="font-medium text-gray-600 dark:text-gray-400">Grado:</span> <span id="bas-grado"></span></div>
                        <div class="col-span-2"><span class="font-medium text-gray-600 dark:text-gray-400">Nombres:</span> <span id="bas-nombre"></span></div>
                        <div><span class="font-medium text-gray-600 dark:text-gray-400">Promoción:</span> <span id="bas-promocion"></span></div>
                        <div><span class="font-medium text-gray-600 dark:text-gray-400">Tipo de traslado:</span> <span id="bas-traslado"></span></div>
                        <div class="col-span-2"><span class="font-medium text-gray-600 dark:text-gray-400">Función efectiva:</span> <span id="bas-funcion"></span></div>
                        <div><span class="font-medium text-gray-600 dark:text-gray-400">Fecha efectiva:</span> <span id="bas-fecha"></span></div>
                        <div><span class="font-medium text-gray-600 dark:text-gray-400">Distrito:</span> <span id="bas-distrito"></span></div>
                        <div class="col-span-2"><span class="font-medium text-gray-600 dark:text-gray-400">Nomenclatura efectiva:</span> <span id="bas-nomen"></span></div>
                    </div>

                    <div id="bas-alerta" class="hidden mt-2">
                        <div class="inline-flex items-start text-red-600 dark:text-red-400">
                            <svg class="w-5 h-5 mr-2 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.721-1.36 3.486 0l6.518 11.596c.75 1.335-.213 2.998-1.743 2.998H3.482c-1.53 0-2.492-1.663-1.743-2.998L8.257 3.1zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V8a1 1 0 112 0v3a1 1 0 01-1 1z"/></svg>
                            <div class="text-sm">
                                El grado <strong>no</strong> coincide con el grado orgánico requerido para su cargo.<br>
                                <span class="font-medium">Grados válidos (orgánico):</span>
                                <span id="bas-validos" class="whitespace-normal"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end p-4 border-t dark:border-gray-700">
                    <button data-modal-hide="basModal" type="button" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/flowbite@2.5.1/dist/flowbite.turbo.js"></script>

    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-bas');
            if (!btn) return;

            const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val ?? '-'; };

            set('bas-cedula',   btn.dataset.cedula);
            set('bas-nombre',   btn.dataset.nombre);
            set('bas-grado',    btn.dataset.grado);
            set('bas-promocion',btn.dataset.promocion);
            set('bas-funcion',  btn.dataset.funcion);
            set('bas-fecha',    btn.dataset.fecha);
            set('bas-traslado', btn.dataset.traslado);
            set('bas-nomen',    btn.dataset.nomen);
            set('bas-distrito', btn.dataset.distrito);

            const validos = (btn.dataset.validos || '').trim();
            set('bas-validos', validos || 'No definido');

            const alerta = (btn.dataset.alerta === '1');
            document.getElementById('bas-alerta')?.classList.toggle('hidden', !alerta);
        });

        // Backdrop oscuro sincronizado
        (function(){
            const modal = document.getElementById('basModal');
            const backdrop = document.getElementById('basModalBackdrop');
            if (!modal || !backdrop) return;

            const sync = () => {
                backdrop.classList.toggle('hidden', modal.classList.contains('hidden'));
            };
            sync();
            new MutationObserver(sync).observe(modal, { attributes: true, attributeFilter: ['class'] });
            backdrop.addEventListener('click', () => modal.querySelector('[data-modal-hide="basModal"]')?.click());
            document.addEventListener('keydown', e => { if (e.key === 'Escape') setTimeout(sync, 50); });
        })();
    </script>
@endsection
