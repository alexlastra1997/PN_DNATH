@extends('layouts.app')

@section('content')
    <section class="bg-gray-50 dark:bg-gray-900 py-6">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-8">

            {{-- Título --}}
            <div class="mb-6">
                <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Importar Usuarios desde Excel</h1>
                <p class="text-sm text-gray-600 dark:text-gray-300">Carga un archivo .xlsx / .xls con cabeceras. El sistema normaliza cédula y fechas.</p>
            </div>

            {{-- Mensajes de estado --}}
            @if(session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('delete'))
                <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 text-yellow-800 px-4 py-3">
                    {{ session('delete') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulario de importación --}}
            <div class="mb-6 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                <form action="{{ route('importar.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Archivo Excel</label>
                        <input type="file" name="archivo" accept=".xlsx,.xls"
                               class="block w-full text-sm file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:bg-gray-100 file:text-gray-700
                        hover:file:bg-gray-200
                        dark:file:bg-gray-700 dark:file:text-gray-200 dark:hover:file:bg-gray-600
                        border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium
                         bg-blue-600 hover:bg-blue-700 text-white shadow">
                            Importar
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">El importador normaliza cabeceras (acentos/espacios), cédulas (solo dígitos, 10) y cualquier campo que contenga “fecha” → Y-m-d.</p>
                </form>
            </div>

            {{-- Botón para vaciar tabla (opcional) --}}
            <div class="mb-6">
                <form action="{{ route('importar.eliminar') }}" method="POST"
                      onsubmit="return confirm('¿Seguro que deseas vaciar la tabla usuarios?');">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium
                       bg-red-600 hover:bg-red-700 text-white shadow">
                        Vaciar tabla usuarios
                    </button>
                </form>
            </div>

            {{-- Resumen de la importación --}}
            @php $resumen = session('resumen'); @endphp
            @if($resumen)
                <div class="mb-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Insertados / Actualizados</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ number_format($resumen['insertados_actualizados'] ?? 0) }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Errores detectados</div>
                        <div class="mt-1 text-2xl font-semibold text-red-600">
                            {{ count($resumen['errores'] ?? []) }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Sugerencia</div>
                        <div class="mt-1 text-sm text-gray-800 dark:text-gray-200">
                            Revisa <code>storage/logs/laravel.log</code> para detalles por fila/cedula.
                        </div>
                    </div>
                </div>

                {{-- Tabla de Errores (colapsable) --}}
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <button type="button" onclick="document.getElementById('tabla-errores').classList.toggle('hidden')"
                            class="w-full text-left px-5 py-3 border-b border-gray-200 dark:border-gray-700
                       text-sm font-medium text-gray-800 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Mostrar / Ocultar detalles de errores
                    </button>
                    <div id="tabla-errores" class="{{ count($resumen['errores'] ?? []) ? '' : 'hidden' }}">
                        @if(count($resumen['errores'] ?? []))
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 text-left">#</th>
                                        <th class="px-3 py-2 text-left">Fila</th>
                                        <th class="px-3 py-2 text-left">Cédula</th>
                                        <th class="px-3 py-2 text-left">Motivo</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($resumen['errores'] as $i => $e)
                                        <tr class="text-gray-800 dark:text-gray-200">
                                            <td class="px-3 py-2">{{ $i + 1 }}</td>
                                            <td class="px-3 py-2">{{ $e['fila'] ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $e['cedula'] ?? '-' }}</td>
                                            <td class="px-3 py-2">{{ $e['motivo'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">No se registraron errores.</div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </section>
@endsection
