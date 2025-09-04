@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 py-4">
        <div class="mx-auto max-w-screen-2xl px-4 lg:px-8">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Carrito de calificaciones</h1>
                <div class="flex gap-2">
                    <a href="{{ route('usuarios.resultados') }}" class="px-3 py-2 rounded-md bg-primary-700 text-white text-sm hover:bg-primary-800">Volver a resultados</a>
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
                                <th class="px-3 py-2 text-right">Acciones</th>
                            </tr>
                            </thead>
                            <tbody id="tbody-aptos">
                            @forelse($aptos as $row)
                                <tr id="row-APTO-{{ $row['cedula'] }}" class="border-b dark:border-gray-700">
                                    <td class="px-3 py-2 font-mono">{{ $row['cedula'] }}</td>
                                    <td class="px-3 py-2">{{ $row['apellidos_nombres'] }}</td>
                                    <td class="px-3 py-2">{{ $row['grado'] }}</td>
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
                                    <td colspan="4" class="px-3 py-6 text-center text-gray-400">Sin registros</td>
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
                                <th class="px-3 py-2 text-right">Acciones</th>
                            </tr>
                            </thead>
                            <tbody id="tbody-no-aptos">
                            @forelse($noAptos as $row)
                                <tr id="row-NO_APTO-{{ $row['cedula'] }}" class="border-b dark:border-gray-700">
                                    <td class="px-3 py-2 font-mono">{{ $row['cedula'] }}</td>
                                    <td class="px-3 py-2">{{ $row['apellidos_nombres'] }}</td>
                                    <td class="px-3 py-2">{{ $row['grado'] }}</td>
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
                                    <td colspan="4" class="px-3 py-6 text-center text-gray-400">Sin registros</td>
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
                        tr.innerHTML = '<td colspan="4" class="px-3 py-6 text-center text-gray-400">Sin registros</td>';
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
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ cedula, estado })
                        });
                        const data = await res.json();

                        if (data.status === 'ok' || data.status === 'missing') {
                            // Quita la fila del DOM si existe
                            const rowId = `row-${estado}-${cedula}`;
                            const row   = document.getElementById(rowId);
                            if (row) row.remove();

                            // Actualiza contadores y estados vacíos
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
