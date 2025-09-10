@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 py-3 sm:py-5">
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-12">

            {{-- Título --}}
            <div class="mb-4">
                <h1 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Generar Pases</h1>
                <p class="text-xs text-gray-500 dark:text-gray-300">Filtra y define requerimientos por grado.</p>
            </div>

            <form method="GET" action="{{ route('generar_pases.index') }}"
                  class="mb-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                <div class="grid grid-cols-12 gap-3 text-xs">

                    {{-- ====== BÁSICOS ====== --}}
                    <div class="col-span-12 md:col-span-3">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Cédula</label>
                        <input type="text" name="cedula" value="{{ request('cedula') }}"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                    </div>

                    <div class="col-span-12 md:col-span-3">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Apellidos y Nombres</label>
                        <input type="text" name="apellidos_nombres" value="{{ request('apellidos_nombres') }}"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                    </div>

                    <div class="col-span-6 md:col-span-2">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Grado</label>
                        <select name="grado" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                            <option value="">— Todos —</option>
                            @foreach($gradosOrdenados as $g)
                                <option value="{{ $g }}" @selected(request('grado')===$g)>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-6 md:col-span-2">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Sexo</label>
                        <select name="sexo" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                            <option value="">— Todos —</option>
                            @foreach($sexos as $s)
                                <option value="{{ $s }}" @selected(request('sexo')===$s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-6 md:col-span-2">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Tipo Personal</label>
                        <select name="tipo_personal" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                            <option value="">— Todos —</option>
                            @foreach($tiposPersonal as $tp)
                                <option value="{{ $tp }}" @selected(request('tipo_personal')===$tp)>{{ $tp }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ====== PROMOCIÓN (múltiple) ====== --}}
                    @php $promSel = $promSel ?? []; @endphp
                    <div class="col-span-12 md:col-span-6">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Promoción (múltiple)</label>
                        <input type="text" id="prom-q" placeholder="Buscar promoción…"
                               class="w-full mb-2 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                        <div id="prom-list" class="h-44 overflow-y-auto rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1">
                            @foreach($promociones as $p)
                                <label class="flex items-center gap-2 text-xs text-gray-800 dark:text-gray-200">
                                    <input type="checkbox" class="prom-item" name="promocion[]" value="{{ $p }}" @checked(in_array($p,$promSel))>
                                    <span>{{ $p }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="button" id="prom-all"      class="text-[11px] px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200">Seleccionar todo</button>
                            <button type="button" id="prom-filtered" class="text-[11px] px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-200">Seleccionar filtrados</button>
                            <button type="button" id="prom-clear"    class="text-[11px] px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-100">Limpiar</button>
                        </div>
                    </div>

                    {{-- ====== PROVINCIA_TRABAJA (múltiple) ====== --}}
                    @php $provTrabSel = $provTrabSel ?? []; @endphp
                    <div class="col-span-12 md:col-span-6">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Provincia (trabaja) – múltiple</label>
                        <input type="text" id="prov-q" placeholder="Buscar provincia…"
                               class="w-full mb-2 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                        <div id="prov-list" class="h-44 overflow-y-auto rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1">
                            @foreach($provinciasTrab as $pv)
                                <label class="flex items-center gap-2 text-xs text-gray-800 dark:text-gray-200">
                                    <input type="checkbox" class="prov-item" name="provincia_trabaja[]" value="{{ $pv }}" @checked(in_array($pv,$provTrabSel))>
                                    <span>{{ $pv }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="button" id="prov-all"      class="text-[11px] px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200">Seleccionar todo</button>
                            <button type="button" id="prov-filtered" class="text-[11px] px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-200">Seleccionar filtrados</button>
                            <button type="button" id="prov-clear"    class="text-[11px] px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-100">Limpiar</button>
                        </div>
                    </div>

                    {{-- ====== ALERTAS (múltiple) ====== --}}
                    @php $alertsSelKeys = $alertsSelKeys ?? []; @endphp
                    <div class="col-span-12">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Alertas (múltiple)</label>
                        <input type="text" id="alert-q" placeholder="Buscar alerta…"
                               class="w-full mb-2 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                        <div id="alert-list" class="h-44 overflow-y-auto rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1">
                            @foreach($alertCatalog as $a)
                                <label class="flex items-center gap-2 text-xs text-gray-800 dark:text-gray-200">
                                    <input type="checkbox" class="alert-item" name="alertas_sel_key[]" value="{{ $a['key'] }}" @checked(in_array($a['key'], $alertsSelKeys))>
                                    <span>{{ $a['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-[11px] mt-1 text-gray-500 dark:text-gray-400">
                            Filtra por coincidencias en <code>alertas</code>, <code>alerta_devengacion</code> o <code>alerta_marco_legal</code>.
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="button" id="alert-all"      class="text-[11px] px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200">Seleccionar todo</button>
                            <button type="button" id="alert-filtered" class="text-[11px] px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-200">Seleccionar filtrados</button>
                            <button type="button" id="alert-clear"    class="text-[11px] px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-100">Limpiar</button>
                        </div>
                    </div>

                    {{-- ====== FECHA EFECTIVA (buckets) ====== --}}
                    @php $fechaSel = $fechaSel ?? []; @endphp
                    <div class="col-span-12 md:col-span-6">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Fecha efectiva (múltiple)</label>
                        <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1">
                            @php $FECHA = ['lt1'=>'Menos de 1 año','gte1'=>'Más de 1 año','gte2'=>'Más de 2 años','gte5'=>'Más de 5 años']; @endphp
                            @foreach($FECHA as $k=>$txt)
                                <label class="inline-flex items-center gap-2 mr-4 text-xs text-gray-800 dark:text-gray-200">
                                    <input type="checkbox" name="fecha_efectiva_bucket[]" value="{{ $k }}" @checked(in_array($k,$fechaSel))>
                                    <span>{{ $txt }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- ====== FASE MATERNIDAD O LACTANCIA ====== --}}
                    @php $faseMLSel = $faseMLSel ?? []; @endphp
                    <div class="col-span-12 md:col-span-6">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Fase maternidad o lactancia (múltiple)</label>
                        <input type="text" id="fase-ml-q" placeholder="Buscar fase…"
                               class="w-full mb-2 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                        <div id="fase-ml-list" class="h-44 overflow-y-auto rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1">
                            @foreach($faseMLCatalog as $opt)
                                <label class="flex items-center gap-2 text-xs text-gray-800 dark:text-gray-200">
                                    <input type="checkbox" class="fase-ml-item" name="fase_ml[]" value="{{ $opt }}" @checked(in_array($opt,$faseMLSel))>
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="button" id="fase-ml-all"      class="text-[11px] px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200">Seleccionar todo</button>
                            <button type="button" id="fase-ml-filtered" class="text-[11px] px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-200">Seleccionar filtrados</button>
                            <button type="button" id="fase-ml-clear"    class="text-[11px] px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-100">Limpiar</button>
                        </div>
                    </div>

                    {{-- ====== Nomenclatura efectiva (múltiple) ====== --}}
                    @php $nomSel = $nomSel ?? []; @endphp
                    <div class="col-span-12 md:col-span-6">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Nomenclatura efectiva (múltiple)</label>
                        <input type="text" id="nom-q" placeholder="Buscar nomenclatura…"
                               class="w-full mb-2 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                        <div id="nom-list" class="h-44 overflow-y-auto rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1">
                            @foreach($nomenclaturas as $opt)
                                <label class="flex items-center gap-2 text-xs text-gray-800 dark:text-gray-200">
                                    <input type="checkbox" class="nom-item" name="nomenclatura_efectiva[]" value="{{ $opt }}" @checked(in_array($opt,$nomSel))>
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="button" id="nom-all"      class="text-[11px] px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200">Seleccionar todo</button>
                            <button type="button" id="nom-filtered" class="text-[11px] px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-200">Seleccionar filtrados</button>
                            <button type="button" id="nom-clear"    class="text-[11px] px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-100">Limpiar</button>
                        </div>
                    </div>

                    {{-- ====== Función efectiva (múltiple) ====== --}}
                    @php $funSel = $funSel ?? []; @endphp
                    <div class="col-span-12 md:col-span-6">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Función efectiva (múltiple)</label>
                        <input type="text" id="fun-q" placeholder="Buscar función…"
                               class="w-full mb-2 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                        <div id="fun-list" class="h-44 overflow-y-auto rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1">
                            @foreach($funciones as $opt)
                                <label class="flex items-center gap-2 text-xs text-gray-800 dark:text-gray-200">
                                    <input type="checkbox" class="fun-item" name="funcion_efectiva[]" value="{{ $opt }}" @checked(in_array($opt,$funSel))>
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="button" id="fun-all"      class="text-[11px] px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200">Seleccionar todo</button>
                            <button type="button" id="fun-filtered" class="text-[11px] px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-200">Seleccionar filtrados</button>
                            <button type="button" id="fun-clear"    class="text-[11px] px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-100">Limpiar</button>
                        </div>
                    </div>

                    {{-- ====== Estado efectivo (múltiple) ====== --}}
                    @php $estSel = $estSel ?? []; @endphp
                    <div class="col-span-12 md:col-span-6">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Estado efectivo (múltiple)</label>
                        <input type="text" id="est-q" placeholder="Buscar estado…"
                               class="w-full mb-2 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                        <div id="est-list" class="h-44 overflow-y-auto rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-2 space-y-1">
                            @foreach($estadosEfectivos as $opt)
                                <label class="flex items-center gap-2 text-xs text-gray-800 dark:text-gray-200">
                                    <input type="checkbox" class="est-item" name="estado_efectivo[]" value="{{ $opt }}" @checked(in_array($opt,$estSel))>
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="button" id="est-all"      class="text-[11px] px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-200">Seleccionar todo</button>
                            <button type="button" id="est-filtered" class="text-[11px] px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-200">Seleccionar filtrados</button>
                            <button type="button" id="est-clear"    class="text-[11px] px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-100">Limpiar</button>
                        </div>
                    </div>

                    {{-- ====== BANDERAS A EXCLUIR ====== --}}
                    @php
                        $flagsSel = $flagsSel ?? [];
                        $FLAGS = [
                          'contrato_estudios'          => 'Contrato estudios',
                          'conyuge_policia_activo'     => 'Cónyuge policía activo',
                          'enf_catast_sp'              => 'Enf. catastrófica SP',
                          'enf_catast_conyuge_hijos'   => 'Enf. catast. C/H',
                          'discapacidad_sp'            => 'Discapacidad SP',
                          'discapacidad_conyuge_hijos' => 'Discapacidad C/H',
                          'novedad_situacion'          => 'Novedad situación',
                          'observacion_tenencia'       => 'Observación tenencia',
                          'alertas_problemas_salud'    => 'Alerta problemas de salud',
                        ];
                    @endphp
                    <div class="col-span-12">
                        <label class="block mb-1 text-gray-700 dark:text-gray-200">Banderas a excluir</label>
                        <div class="rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                            <div class="flex items-center justify-between">
                                <label class="inline-flex items-center gap-2 text-xs text-gray-800 dark:text-gray-200">
                                    <input type="checkbox" id="flags-all">
                                    <span class="font-medium">Seleccionar todo</span>
                                </label>
                                <button type="button" id="flags-clear" class="text-[11px] px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-600">Limpiar</button>
                            </div>

                            <div id="flags-container" class="flex flex-wrap gap-3 items-center mt-2">
                                @foreach($FLAGS as $key => $label)
                                    <label class="inline-flex items-center gap-2 text-xs text-gray-800 dark:text-gray-200">
                                        <input type="checkbox" name="flags[]" value="{{ $key }}" @checked(in_array($key,$flagsSel)) class="flag-item">
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <p class="text-[11px] mt-2 text-gray-500 dark:text-gray-400">
                                Se excluirán los usuarios que tengan <strong>cualquiera</strong> de las banderas seleccionadas.
                            </p>
                        </div>
                    </div>

                    {{-- ====== REQUERIMIENTOS POR GRADO ====== --}}
                    <div class="col-span-12">
                        <h3 class="text-xs font-semibold text-gray-800 dark:text-gray-100 mb-2">Requerimientos por grado</h3>
                        <div class="grid grid-cols-12 gap-3">
                            @foreach($gradosOrdenados as $g)
                                <div class="col-span-6 md:col-span-2">
                                    <label class="block mb-1 text-gray-700 dark:text-gray-200">{{ $g }}</label>
                                    <input type="number" min="0" name="req_grados[{{ $g }}]" value="{{ (int) data_get(request()->query('req_grados',[]), $g, 0) }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-100">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="col-span-12 flex gap-2 mt-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-xs hover:bg-blue-700">Aplicar filtros</button>
                        <a href="{{ route('generar_pases.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-md text-xs">Limpiar</a>
                    </div>

                </div>
            </form>

            {{-- ===== RESULTADOS ===== --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100">
                    <tr>
                        <th class="px-2 py-2 text-left">Cédula</th>
                        <th class="px-2 py-2 text-left">Apellidos Nombres</th>
                        <th class="px-2 py-2 text-left">Grado</th>
                        <th class="px-2 py-2 text-left">Sexo</th>
                        <th class="px-2 py-2 text-left">Tipo</th>
                        <th class="px-2 py-2 text-left">Fecha efectiva</th>
                        <th class="px-2 py-2 text-left">Nomenclatura efectiva</th>
                        <th class="px-2 py-2 text-left">Función efectiva</th>
                        <th class="px-2 py-2 text-left">Estado efectivo</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-800 dark:text-gray-100">
                    @forelse($usuarios as $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-2 py-2">{{ $u->cedula }}</td>
                            <td class="px-2 py-2">{{ $u->apellidos_nombres }}</td>
                            <td class="px-2 py-2">{{ $u->grado }}</td>
                            <td class="px-2 py-2">{{ $u->sexo }}</td>
                            <td class="px-2 py-2">{{ $u->tipo_personal }}</td>
                            <td class="px-2 py-2">{{ $u->fecha_efectiva }}</td>
                            <td class="px-2 py-2">{{ $u->nomenclatura_efectiva }}</td>
                            <td class="px-2 py-2">{{ $u->funcion_efectiva }}</td>
                            <td class="px-2 py-2">{{ $u->estado_efectivo }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-2 py-6 text-center text-gray-500 dark:text-gray-300">No hay resultados</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="px-3 py-2">
                    {{ $usuarios->links() }}
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        /**
         * Inicializa un multiselect con:
         *  - input de búsqueda:  #<pref>-q
         *  - lista contenedora:  #<pref>-list
         *  - items (labels):     label dentro de la lista
         *  - checkboxes:         .<pref>-item
         *  - botones:
         *      #<pref>-all, #<pref>-filtered, #<pref>-clear
         */
        function initMulti(prefix) {
            const q   = document.getElementById(prefix + '-q');
            const box = document.getElementById(prefix + '-list');
            const all = document.getElementById(prefix + '-all');
            const fil = document.getElementById(prefix + '-filtered');
            const clr = document.getElementById(prefix + '-clear');

            if (!box) return;

            function labels(){ return box.querySelectorAll('label'); }
            function cbOf(label){ return label.querySelector('input[type=checkbox]'); }

            // Buscar
            q && q.addEventListener('input', function(){
                const term = this.value.trim().toLowerCase();
                labels().forEach(l => {
                    const text = l.textContent.trim().toLowerCase();
                    l.style.display = text.includes(term) ? '' : 'none';
                });
            });

            // Seleccionar todo
            all && all.addEventListener('click', () => {
                box.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = true);
            });

            // Seleccionar filtrados (solo visibles)
            fil && fil.addEventListener('click', () => {
                labels().forEach(l => { if (l.style.display !== 'none') cbOf(l).checked = true; });
            });

            // Limpiar
            clr && clr.addEventListener('click', () => {
                box.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
                if (q) { q.value=''; labels().forEach(l => l.style.display=''); }
            });
        }

        // Inits para todos los selectores múltiples
        ['prom','prov','alert','nom','fun','est','fase-ml'].forEach(initMulti);

        // Banderas: seleccionar todo / limpiar
        (function(){
            const master = document.getElementById('flags-all');
            const clear  = document.getElementById('flags-clear');
            const cont   = document.getElementById('flags-container');
            if (!cont) return;

            function items(){ return cont.querySelectorAll('.flag-item'); }

            master && master.addEventListener('change', function(){
                items().forEach(cb => cb.checked = master.checked);
            });

            clear && clear.addEventListener('click', function(){
                items().forEach(cb => cb.checked = false);
                if (master) master.checked = false;
            });
        })();
    </script>
@endpush
