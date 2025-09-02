{{-- resources/views/reporte_organico/visualizador.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-xl font-bold mb-4">Visualizador Orgánico</h1>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('reporte_organico.visualizador') }}" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="servicio" value="{{ request('servicio') }}" placeholder="Buscar Servicio" class="px-3 py-2 border rounded">
            <input type="text" name="nomenclatura" value="{{ request('nomenclatura') }}" placeholder="Buscar Nomenclatura" class="px-3 py-2 border rounded">
            <input type="text" name="cargo" value="{{ request('cargo') }}" placeholder="Buscar Cargo" class="px-3 py-2 border rounded">

            {{-- Selector de Estado --}}
            <select name="estado" class="px-3 py-2 border rounded">
                <option value="">-- Estado --</option>
                <option value="COMPLETO" {{ request('estado') == 'COMPLETO' ? 'selected' : '' }}>COMPLETO</option>
                <option value="EXCEDIDO" {{ request('estado') == 'EXCEDIDO' ? 'selected' : '' }}>EXCEDIDO</option>
                <option value="VACANTE"   {{ request('estado') == 'VACANTE'   ? 'selected' : '' }}>VACANTE</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded w-full md:w-auto">
                    Buscar
                </button>
                <a href="{{ route('reporte_organico.visualizador') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded w-full md:w-auto text-center">
                    Limpiar
                </a>
            </div>
        </form>

        {{-- Exportar Excel (mantiene tus filtros) --}}
        <form action="{{ route('reporte_organico.exportar') }}" method="GET" class="mb-4">
            <input type="hidden" name="servicio"     value="{{ request('servicio') }}">
            <input type="hidden" name="nomenclatura" value="{{ request('nomenclatura') }}">
            <input type="hidden" name="cargo"        value="{{ request('cargo') }}">
            <input type="hidden" name="estado"       value="{{ request('estado') }}">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Descargar Excel
            </button>
        </form>

        {{-- Accordion: Conteo por Servicio (Flowbite data-accordion) --}}
        {{-- Accordion nativo: Conteo por Servicio (sin JS, sin Flowbite) --}}
        @if(isset($conteoServicios) && $conteoServicios->count())
            <details class="mb-6 group bg-white border border-gray-200 rounded-lg">
                <summary class="list-none cursor-pointer select-none flex items-center justify-between w-full p-4 font-medium text-gray-700 hover:bg-gray-50 rounded-lg">
                    <span>Conteo por Servicio</span>
                    {{-- Icono chevron (rota cuando está abierto gracias a group-open) --}}
                    <svg class="w-5 h-5 transition-transform duration-200 group-open:rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6" aria-hidden="true">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </summary>

                <div class="p-4 pt-0">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($conteoServicios as $svc)
                            <div class="bg-white border border-gray-200 rounded-2xl shadow p-4">
                                <div class="text-xs uppercase tracking-wide text-gray-500">Servicio</div>
                                <div class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $svc->servicio_organico ?: 'Sin servicio' }}
                                </div>
                                <div class="mt-3 text-3xl font-extrabold">{{ number_format($svc->total) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </details>
        @endif


        {{-- CARDS TOTALES (Aprobado / Efectivo / Nivel Adscrito) --}}
        @php
            $aprobadoTotal = isset($totales) ? (int)($totales->total_aprobado ?? 0) : 0;
            $efectivoTotal = isset($totales) ? (int)($totales->total_efectivo ?? 0) : 0;
            $nadsAprobado  = isset($nivelAdscrito) ? (int)($nivelAdscrito->total_aprobado ?? 0) : 0;
            $nadsEfectivo  = isset($nivelAdscrito) ? (int)($nivelAdscrito->total_efectivo ?? 0) : 0;
        @endphp

            <!-- Botón abre modal -->
        <button
            type="button"
            id="btn-open-cards"
            class="mb-4 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
            Ver resumen (tabla)
        </button>

        <!-- ... aquí sigue tu tabla de $datos ... -->

        <!-- ... aquí sigue la paginación ... -->

        <!-- Modal con tabla de 3 columnas -->
        <div id="cards-modal-backdrop"
             class="fixed inset-0 bg-black/50 z-40 hidden"></div>

        <div id="cards-modal"
             class="fixed inset-0 z-50 hidden flex items-center justify-center p-4"
             role="dialog" aria-modal="true" aria-labelledby="cards-modal-title">
            <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 id="cards-modal-title" class="text-lg font-semibold text-gray-800">
                        Resumen (según filtros actuales)
                    </h3>
                    <button type="button" id="btn-close-cards"
                            class="text-gray-500 hover:text-gray-700 p-2 rounded">
                        ✕
                    </button>
                </div>

                <!-- Aquí pegas la tabla de 3 columnas que te pasé -->
                <div class="px-6 py-6">
                    @php
                        // Totales globales (ya los tenías)
                        $aprobadoTotal = isset($totales) ? (int)($totales->total_aprobado ?? 0) : 0;
                        $efectivoTotal = isset($totales) ? (int)($totales->total_efectivo ?? 0) : 0;

                        // Conteos por estado_efectivo (asegúrate de pasar $estadoEfectivo desde el controlador)
                        $tt = ($estadoEfectivo['TRASLADO TEMPORAL'] ?? 0) + ($estadoEfectivo['TRASLADO TEMPORAL POR EXCEDENTE'] ?? 0);
                        $te = isset($estadoEfectivo) ? (int)($estadoEfectivo['TRASLADO EVENTUAL'] ?? 0) : 0;
                        $uo = isset($estadoEfectivo) ? (int)($estadoEfectivo['UNIDAD DE ORIGEN']  ?? 0) : 0;

                        // Otros niveles (si ya los pasaste)
                        $asesorAp  = isset($nivelAsesor)? (int)($nivelAsesor->total_aprobado ?? 0) : 0;
                        $asesorEf  = isset($nivelAsesor)? (int)($nivelAsesor->total_efectivo ?? 0) : 0;

                        $apoyoAp   = isset($nivelApoyo)? (int)($nivelApoyo->total_aprobado ?? 0) : 0;
                        $apoyoEf   = isset($nivelApoyo)? (int)($nivelApoyo->total_efectivo ?? 0) : 0;

                        $coordAp   = isset($nivelCoordinacion)? (int)($nivelCoordinacion->total_aprobado ?? 0) : 0;
                        $coordEf   = isset($nivelCoordinacion)? (int)($nivelCoordinacion->total_efectivo ?? 0) : 0;

                        $desconcAp = isset($nivelDesconcentrado)? (int)($nivelDesconcentrado->total_aprobado ?? 0) : 0;
                        $desconcEf = isset($nivelDesconcentrado)? (int)($nivelDesconcentrado->total_efectivo ?? 0) : 0;

                        $ndescZonalAp = isset($ndescZonal)? (int)($ndescZonal->total_aprobado ?? 0) : 0;
                        $ndescZonalEf = isset($ndescZonal)? (int)($ndescZonal->total_efectivo ?? 0) : 0;

                        $ndescSubAp = isset($ndescSubzonal)? (int)($ndescSubzonal->total_aprobado ?? 0) : 0;
                        $ndescSubEf = isset($ndescSubzonal)? (int)($ndescSubzonal->total_efectivo ?? 0) : 0;

                        $ndescDcsAp = isset($ndescDCS)? (int)($ndescDCS->total_aprobado ?? 0) : 0;
                        $ndescDcsEf = isset($ndescDCS)? (int)($ndescDCS->total_efectivo ?? 0) : 0;

                        $jefPrevAp = isset($jefPrev)? (int)($jefPrev->total_aprobado ?? 0) : 0;
                        $jefPrevEf = isset($jefPrev)? (int)($jefPrev->total_efectivo ?? 0) : 0;

                        $jefInvAp = isset($jefInv)? (int)($jefInv->total_aprobado ?? 0) : 0;
                        $jefInvEf = isset($jefInv)? (int)($jefInv->total_efectivo ?? 0) : 0;

                        $jefIntAp = isset($jefInt)? (int)($jefInt->total_aprobado ?? 0) : 0;
                        $jefIntEf = isset($jefInt)? (int)($jefInt->total_efectivo ?? 0) : 0;

                        $directAp  = isset($nivelDirectivo)? (int)($nivelDirectivo->total_aprobado ?? 0) : 0;
                        $directEf  = isset($nivelDirectivo)? (int)($nivelDirectivo->total_efectivo ?? 0) : 0;

                        $operaAp   = isset($nivelOperativo)? (int)($nivelOperativo->total_aprobado ?? 0) : 0;
                        $operaEf   = isset($nivelOperativo)? (int)($nivelOperativo->total_efectivo ?? 0) : 0;
                    @endphp

                    <div class="overflow-x-auto">
                        <table class="min-w-full border text-sm text-gray-800">
                            <thead class="bg-gray-100 font-semibold uppercase text-xs">
                            <tr>
                                <th class="px-4 py-2 border">Nivel</th>
                                <th class="px-4 py-2 border text-center">Aprobado</th>
                                <th class="px-4 py-2 border text-center">Efectivo</th>
                                <th class="px-4 py-2 border text-center">Traslado Temporal</th>
                                <th class="px-4 py-2 border text-center">Traslado Eventual</th>
                                <th class="px-4 py-2 border text-center">Unidad de Origen</th>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- Totales --}}
                            <tr>
                                <td class="px-4 py-2 border font-medium">Orgánico Aprobado (Total)</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($aprobadoTotal) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border font-medium">Orgánico Efectivo (Total)</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($efectivoTotal) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($tt) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($te) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($uo) }}</td>
                            </tr>

                            {{-- Niveles principales --}}
                            <tr>
                                <td class="px-4 py-2 border font-medium">Nivel Asesor (NAS)</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($asesorAp) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($asesorEf) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border font-medium">Nivel de Apoyo (NAP)</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($apoyoAp) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($apoyoEf) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border font-medium">Nivel de Coordinación (NCOORD)</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($coordAp) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($coordEf) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>

                            {{-- NDESC (Total + subdivisiones) --}}
                            <tr class="bg-orange-50">
                                <td class="px-4 py-2 border font-semibold text-orange-800">Nivel Desconcentrado (NDESC) — Total</td>
                                <td class="px-4 py-2 border text-center font-semibold text-orange-800">{{ number_format($desconcAp) }}</td>
                                <td class="px-4 py-2 border text-center font-semibold text-orange-800">{{ number_format($desconcEf) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border pl-8">ZONAL (Servicio contiene “PREV-ZONAL”)</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($ndescZonalAp) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($ndescZonalEf) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border pl-8">SUBZONAL (Servicio contiene “PREV-SZ”)</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($ndescSubAp) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($ndescSubEf) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border pl-8">DISTRITO – CIRCUITO – SUBCIRCUITO (Servicio “PREV-D-C-S”)</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($ndescDcsAp) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($ndescDcsEf) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>

                            {{-- Jefaturas por NOMENCLATURA --}}
                            <tr class="bg-sky-50">
                                <td class="px-4 py-2 border font-semibold text-sky-800">Jefatura Preventiva (JPREV)</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($jefPrevAp) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($jefPrevEf) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>
                            <tr class="bg-sky-50">
                                <td class="px-4 py-2 border font-semibold text-sky-800">Jefatura de Investigación (JINV)</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($jefInvAp) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($jefInvEf) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>
                            <tr class="bg-sky-50">
                                <td class="px-4 py-2 border font-semibold text-sky-800">Jefatura de Inteligencia (JINT)</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($jefIntAp) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($jefIntEf) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>

                            {{-- Otros niveles --}}
                            <tr>
                                <td class="px-4 py-2 border font-medium">Nivel Directivo (NDIREC)</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($directAp) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($directEf) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 border font-medium">Nivel Operativo (NOPERA)</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($operaAp) }}</td>
                                <td class="px-4 py-2 border text-center">{{ number_format($operaEf) }}</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                                <td class="px-4 py-2 border text-center">—</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Form oculto para exportar en Excel con filtros vigentes --}}
                    <form id="form-export-resumen-xlsx" action="{{ route('reporte_organico.exportar_resumen_xlsx') }}" method="GET" class="hidden">
                        <input type="hidden" name="servicio"     value="{{ request('servicio') }}">
                        <input type="hidden" name="nomenclatura" value="{{ request('nomenclatura') }}">
                        <input type="hidden" name="cargo"        value="{{ request('cargo') }}">
                        <input type="hidden" name="estado"       value="{{ request('estado') }}">
                    </form>

                    <div class="px-6 py-4 border-t flex gap-2 justify-end">
                        <button type="button" id="btn-close-cards-2"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded">
                            Cerrar
                        </button>
                        <button type="button"
                                onclick="document.getElementById('form-export-resumen-xlsx').submit()"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded">
                            Descargar Excel
                        </button>
                    </div>


                </div>


            </div>
        </div>

        <!-- Script abrir/cerrar modal -->
        <script>
            (function () {
                const openBtn   = document.getElementById('btn-open-cards');
                const closeBtn  = document.getElementById('btn-close-cards');
                const closeBtn2 = document.getElementById('btn-close-cards-2');
                const modal     = document.getElementById('cards-modal');
                const backdrop  = document.getElementById('cards-modal-backdrop');

                function openModal() {
                    modal.classList.remove('hidden');
                    backdrop.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                }
                function closeModal() {
                    modal.classList.add('hidden');
                    backdrop.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }

                if (openBtn)  openBtn.addEventListener('click', openModal);
                if (closeBtn) closeBtn.addEventListener('click', closeModal);
                if (closeBtn2) closeBtn2.addEventListener('click', closeModal);
                if (backdrop) backdrop.addEventListener('click', closeModal);
            })();
        </script>

        {{-- Tabla (estructura basada en tu vista anterior) --}}
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border text-sm">
                <thead class="bg-gray-100 text-xs font-semibold text-gray-700 uppercase">
                <tr>
                    <th class="px-4 py-2">Servicio</th>
                    <th class="px-4 py-2">Nomenclatura</th>
                    <th class="px-4 py-2">Cargo</th>
                    <th class="px-4 py-2">Orgánico Aprobado</th>
                    <th class="px-4 py-2">Orgánico Efectivo</th>
                    <th class="px-4 py-2">Ver Ocupantes</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($datos as $item)
                    @php
                        $efectivo = $item->organico_efectivo;
                        $aprobado = $item->organico_aprobado;
                        $estado = '';

                        if ($efectivo == $aprobado)      $estado = 'COMPLETO';
                        elseif ($efectivo > $aprobado)    $estado = 'EXCEDIDO';
                        else                               $estado = 'VACANTE';

                        $color = match(true) {
                            $estado === 'COMPLETO' => 'bg-green-500',
                            $estado === 'EXCEDIDO' => 'bg-red-500',
                            $estado === 'VACANTE'  => 'bg-yellow-400',
                            default                => 'bg-gray-400'
                        };
                    @endphp
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $item->servicio_organico }}</td>
                        <td class="px-4 py-2">{{ $item->nomenclatura_organico }}</td>
                        <td class="px-4 py-2">{{ $item->cargo_organico }}</td>
                        <td class="px-4 py-2">{{ $aprobado }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold text-white rounded-full {{ $color }}">
                                {{ $efectivo }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <form action="{{ route('reporte_organico.ocupantes') }}" method="GET">
                                <input type="hidden" name="nomenclatura" value="{{ $item->nomenclatura_organico }}">
                                <input type="hidden" name="cargo"        value="{{ $item->cargo_organico }}">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1 rounded">
                                    Ver Ocupantes
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">No se encontraron resultados.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $datos->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
