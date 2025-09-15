@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 py-6">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-8">
            <div class="mb-6 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <h1 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">
                    Comparar Excel vs Base de Datos (desde fila 20)
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                    Sube el archivo. Se leerán la <strong>columna C</strong> (Cédula) y la <strong>columna G</strong> (Nomenclatura) desde la <strong>fila 20</strong> hasta el final.
                    Se comparará con <code>usuarios.cedula</code> y <code>usuarios.nomenclatura_efectiva</code>.
                </p>

                @if ($errors->any())
                    <div class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-red-700 text-sm">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Formulario de subida (no anidar otros forms dentro) --}}
                <form action="{{ route('comparador.procesar') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Archivo Excel (.xlsx / .xls)</label>
                        <input type="file" name="archivo" accept=".xlsx,.xls"
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none
                        dark:text-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"/>
                    </div>

                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium
                                     bg-blue-600 hover:bg-blue-700 text-white focus:ring-2 focus:ring-blue-300">
                            Procesar
                        </button>
                    </div>
                </form>
            </div>

            @if (!empty($resumen))
                <div class="mb-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-2">Resumen</h2>
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-3 text-sm">
                        <div class="p-3 rounded bg-gray-50 dark:bg-gray-700">
                            <div class="text-gray-500">Fila inicio</div>
                            <div class="font-semibold">{{ $resumen['fila_inicio'] ?? '' }}</div>
                        </div>
                        <div class="p-3 rounded bg-gray-50 dark:bg-gray-700">
                            <div class="text-gray-500">Fila fin</div>
                            <div class="font-semibold">{{ $resumen['fila_fin'] ?? '' }}</div>
                        </div>
                        <div class="p-3 rounded bg-gray-50 dark:bg-gray-700">
                            <div class="text-gray-500">Filas leídas</div>
                            <div class="font-semibold">{{ $resumen['filas_leidas'] ?? '' }}</div>
                        </div>
                        <div class="p-3 rounded bg-gray-50 dark:bg-gray-700">
                            <div class="text-gray-500">Coinciden</div>
                            <div class="font-semibold text-emerald-600">{{ $resumen['coinciden'] ?? 0 }}</div>
                        </div>
                        <div class="p-3 rounded bg-gray-50 dark:bg-gray-700">
                            <div class="text-gray-500">Errores</div>
                            <div class="font-semibold text-red-600">{{ $resumen['errores'] ?? 0 }}</div>
                        </div>
                        <div class="p-3 rounded bg-gray-50 dark:bg-gray-700">
                            <div class="text-gray-500">Saltos (vacías)</div>
                            <div class="font-semibold text-gray-600">{{ $resumen['saltos'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            @endif

            @if (!empty($resultados))
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-3">
                        Discrepancias encontradas ({{ number_format(count($resultados)) }})
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs md:text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Fila (Excel)</th>
                                <th class="px-3 py-2 text-left">Cédula (Excel)</th>
                                <th class="px-3 py-2 text-left">Existe en BD</th>
                                <th class="px-3 py-2 text-left">Nomenclatura (Excel)</th>
                                <th class="px-3 py-2 text-left">Nomenclatura (BD)</th>
                                <th class="px-3 py-2 text-left">Motivo</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($resultados as $r)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-3 py-2">{{ $r['fila_excel'] }}</td>
                                    <td class="px-3 py-2">{{ $r['cedula_excel'] }}</td>
                                    <td class="px-3 py-2">
                                        @if (($r['existe_en_bd'] ?? '') === 'SÍ')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs">SÍ</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 text-red-800 text-xs">NO</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-pre-line">{{ $r['nomenclatura_excel'] }}</td>
                                    <td class="px-3 py-2 whitespace-pre-line">{{ $r['nomenclatura_bd'] }}</td>
                                    <td class="px-3 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs">
                                      {{ $r['motivo'] }}
                                    </span>
                                        </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Formulario separado para exportar (fuera del form de subida) --}}
                    <form action="{{ route('comparador.exportar') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium
                                     bg-emerald-600 hover:bg-emerald-700 text-white focus:ring-2 focus:ring-emerald-300">
                            Exportar informe a Excel
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </section>
@endsection
