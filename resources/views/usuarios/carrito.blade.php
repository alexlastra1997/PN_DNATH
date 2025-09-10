@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 py-4">
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-8">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Carrito de calificaciones</h1>
                <div class="flex gap-2">
                    {{-- Botones header --}}
                    <div class="flex gap-2">
                        <a href="{{ route('usuarios.resultados') }}"
                           class="px-3 py-2 rounded-md bg-primary-700 text-white text-sm hover:bg-primary-800">Volver a resultados</a>

                        {{-- Abrir modal de informe --}}
                        <button type="button" id="btn-open-informe"
                                class="px-3 py-2 rounded-md bg-gray-900 text-white text-sm hover:bg-black">
                            Elaborar informe (PDF)
                        </button>
                    </div>

                    {{-- MODAL: datos del informe --}}
                    <div id="modal-informe" class="fixed inset-0 z-50 hidden">
                        <div class="absolute inset-0 bg-black/40" data-close-informe></div>

                        <div class="relative z-50 mx-auto mt-16 w-[94%] max-w-xl rounded-xl border border-gray-200
              dark:border-gray-700 bg-white dark:bg-gray-800 shadow-2xl">
                            <div class="px-5 py-3 border-b dark:border-gray-700 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Datos del informe</h3>
                                <button type="button" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" data-close-informe>✕</button>
                            </div>

                            {{-- Enviamos por GET a la ruta del PDF y abrimos en nueva pestaña para descargar --}}
                            <form action="{{ route('usuarios.informe.pdf') }}" method="GET" target="_blank" class="px-5 py-4 space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">
                                        Nombre de la capacitación
                                    </label>
                                    <input type="text" name="capacitacion" required
                                           class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                      text-sm focus:ring-0 focus:outline-none"
                                           placeholder="Ej: II Curso Internacional de Especialización en...">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Modalidad</label>
                                        <select name="modalidad" required
                                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none">
                                            <option value="PRESENCIAL">Presencial</option>
                                            <option value="SEMI PRESENCIAL">Semi presencial</option>
                                            <option value="EN LÍNEA">En línea</option>
                                            <option value="INTERNACIONAL">Internacional</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Nro. total (opcional)</label>
                                        <input type="number" name="nro_total_override" min="0"
                                               class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none"
                                               placeholder="Si lo dejas vacío, se calcula automático">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Fecha inicio</label>
                                        <input type="date" name="fecha_inicio" required
                                               class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Fecha fin</label>
                                        <input type="date" name="fecha_fin" required
                                               class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none">
                                    </div>
                                </div>

                                <hr class="border-gray-200 dark:border-gray-700">

                                <div>
                                    <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase">Firmas</div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Elaborado por --}}
                                        <div class="space-y-2">
                                            <div class="text-[11px] font-semibold uppercase text-gray-600 dark:text-gray-300">Elaborado por</div>
                                            <div>
                                                <label class="block text-xs mb-1 text-gray-700 dark:text-gray-300">Nombre</label>
                                                <input type="text" name="elaborado_nombre" required
                                                       class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none"
                                                       placeholder="Nombres y apellidos">
                                            </div>
                                            <div>
                                                <label class="block text-xs mb-1 text-gray-700 dark:text-gray-300">Grado</label>
                                                <input type="text" name="elaborado_grado" required
                                                       class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none"
                                                       placeholder="Ej: Subteniente de Policía">
                                            </div>
                                            <div>
                                                <label class="block text-xs mb-1 text-gray-700 dark:text-gray-300">Cargo</label>
                                                <input type="text" name="elaborado_cargo" required
                                                       class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none"
                                                       placeholder="Ej: Analista del Departamento de Desarrollo Institucional">
                                            </div>
                                        </div>

                                        {{-- Revisado por --}}
                                        <div class="space-y-2">
                                            <div class="text-[11px] font-semibold uppercase text-gray-600 dark:text-gray-300">Revisado por</div>
                                            <div>
                                                <label class="block text-xs mb-1 text-gray-700 dark:text-gray-300">Nombre</label>
                                                <input type="text" name="revisado_nombre" required
                                                       class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none"
                                                       placeholder="Nombres y apellidos">
                                            </div>
                                            <div>
                                                <label class="block text-xs mb-1 text-gray-700 dark:text-gray-300">Grado</label>
                                                <input type="text" name="revisado_grado" required
                                                       class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none"
                                                       placeholder="Ej: Mayor de Policía">
                                            </div>
                                            <div>
                                                <label class="block text-xs mb-1 text-gray-700 dark:text-gray-300">Cargo</label>
                                                <input type="text" name="revisado_cargo" required
                                                       class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm focus:ring-0 focus:outline-none"
                                                       placeholder="Ej: Jefe del Departamento de Desarrollo Institucional (S)">
                                            </div>
                                        </div>
                                    </div>

                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-2">
                                        * Los datos de “Elaborado por” y “Revisado por” se imprimirán debajo de cada anexo, con espacio para firma.
                                    </p>
                                </div>

                                <div class="pt-2 text-right">
                                    <button type="button" class="px-3 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-sm" data-close-informe>Cancelar</button>
                                    <button type="submit" class="ml-2 px-3 py-2 rounded-md bg-primary-700 hover:bg-primary-800 text-sm text-white">Descargar PDF</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- JS para abrir/cerrar modal --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const btnOpen = document.getElementById('btn-open-informe');
                            const modal   = document.getElementById('modal-informe');
                            const closers = modal.querySelectorAll('[data-close-informe]');

                            btnOpen.addEventListener('click', () => modal.classList.remove('hidden'));
                            closers.forEach(b => b.addEventListener('click', () => modal.classList.add('hidden')));
                        });
                    </script>

                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- APTOS --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center justify-between">
                        <h2 class="font-semibold text-green-600 dark:text-green-400">APTOS</h2>
                        <span id="count-aptos" class="text-xs text-gray-500 dark:text-gray-400">{{ count($aptos) }} seleccionado(s)</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="px-3 py-2">CÉDULA</th>
                                <th class="px-3 py-2">APELLIDOS NOMBRES</th>
                                <th class="px-3 py-2">GRADO</th>
                                <th class="px-3 py-2">Novedad</th>
                                <th class="px-3 py-2 text-right">Acciones</th>
                            </tr>
                            </thead>
                            <tbody id="tbody-aptos">
                            @forelse($aptos as $row)
                                <tr id="row-APTO-{{ $row['cedula'] }}" class="border-b dark:border-gray-700">
                                    <td class="px-3 py-2 font-mono">{{ $row['cedula'] }}</td>
                                    <td class="px-3 py-2">{{ $row['apellidos_nombres'] }}</td>
                                    <td class="px-3 py-2">{{ $row['grado'] }}</td>
                                    <td class="px-3 py-2">
                                        @if(($row['novedad'] ?? 'SIN_NOVEDAD') === 'NOVEDAD')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-yellow-100 text-yellow-800 mr-2">NOVEDAD</span>
                                            <span>{{ $row['detalle_novedad'] }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700">SIN NOVEDAD</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <button
                                            class="px-3 py-1 rounded-md bg-red-600 text-white text-xs hover:bg-red-700"
                                            data-remove
                                            data-estado="APTO"
                                            data-cedula="{{ $row['cedula'] }}">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-aptos">
                                    <td colspan="5" class="px-3 py-6 text-center text-gray-400">Sin registros</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- NO APTOS --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b dark:border-gray-700 flex items-center justify-between">
                        <h2 class="font-semibold text-red-600 dark:text-red-400">NO APTOS</h2>
                        <span id="count-no-aptos" class="text-xs text-gray-500 dark:text-gray-400">{{ count($noAptos) }} seleccionado(s)</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="px-3 py-2">CÉDULA</th>
                                <th class="px-3 py-2">APELLIDOS NOMBRES</th>
                                <th class="px-3 py-2">GRADO</th>
                                <th class="px-3 py-2">Novedad</th>
                                <th class="px-3 py-2 text-right">Acciones</th>
                            </tr>
                            </thead>
                            <tbody id="tbody-no-aptos">
                            @forelse($noAptos as $row)
                                <tr id="row-NO_APTO-{{ $row['cedula'] }}" class="border-b dark:border-gray-700">
                                    <td class="px-3 py-2 font-mono">{{ $row['cedula'] }}</td>
                                    <td class="px-3 py-2">{{ $row['apellidos_nombres'] }}</td>
                                    <td class="px-3 py-2">{{ $row['grado'] }}</td>
                                    <td class="px-3 py-2">
                                        @if(($row['novedad'] ?? 'SIN_NOVEDAD') === 'NOVEDAD')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-yellow-100 text-yellow-800 mr-2">NOVEDAD</span>
                                            <span>{{ $row['detalle_novedad'] }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700">SIN NOVEDAD</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <button
                                            class="px-3 py-1 rounded-md bg-red-600 text-white text-xs hover:bg-red-700"
                                            data-remove
                                            data-estado="NO_APTO"
                                            data-cedula="{{ $row['cedula'] }}">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-no-aptos">
                                    <td colspan="5" class="px-3 py-6 text-center text-gray-400">Sin registros</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- JS eliminación --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).content || "{{ csrf_token() }}";
            const URL  = "{{ route('usuarios.carrito.eliminar') }}";

            function updateEmptyState(tbodyId, emptyClass, countId) {
                const tbody = document.getElementById(tbodyId);
                const count = tbody.querySelectorAll('tr[id^="row-"]').length;
                document.getElementById(countId).textContent = count + ' seleccionado(s)';
                if (count === 0) {
                    if (!tbody.querySelector('.' + emptyClass)) {
                        const tr = document.createElement('tr');
                        tr.className = emptyClass;
                        tr.innerHTML = '<td colspan="5" class="px-3 py-6 text-center text-gray-400">Sin registros</td>';
                        tbody.appendChild(tr);
                    }
                }
            }

            document.querySelectorAll('[data-remove]').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const cedula = btn.getAttribute('data-cedula');
                    const estado = btn.getAttribute('data-estado');

                    try {
                        const res = await fetch(URL, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json','X-CSRF-TOKEN': CSRF,'Accept':'application/json' },
                            body: JSON.stringify({ cedula, estado })
                        });
                        const data = await res.json();

                        if (data.status === 'ok' || data.status === 'missing') {
                            const rowId = `row-${estado}-${cedula}`;
                            const row   = document.getElementById(rowId);
                            if (row) row.remove();

                            if (estado === 'APTO') {
                                updateEmptyState('tbody-aptos', 'empty-aptos', 'count-aptos');
                            } else {
                                updateEmptyState('tbody-no-aptos', 'empty-no-aptos', 'count-no-aptos');
                            }
                        } else {
                            alert(data.message || 'No se pudo eliminar.');
                        }
                    } catch (e) {
                        alert('Error de red o servidor.');
                    }
                });
            });
        });
    </script>
@endsection
