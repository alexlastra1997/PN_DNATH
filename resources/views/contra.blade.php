@extends('layouts.app')

@section('content')
    <div x-data="{ open:false, alerta:'', cedulaSeleccionada:'' }" class="max-w-6xl mx-auto p-4 space-y-6">

        {{-- ✅ SECCIÓN A: IMPORTAR --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <h1 class="text-xl font-bold mb-4">📥 Importar Cédulas para Alertas</h1>

            @if(session('success'))
                <div class="mb-4 p-2 bg-green-200 text-green-900 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('contra.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-2 md:items-center">
                @csrf
                <input type="file" name="file" required class="border p-2 rounded w-full md:w-auto">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full md:w-auto">
                    Subir Excel
                </button>
            </form>

            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                * El sistema marca con <b>⚠️</b> únicamente a quienes tengan <b>alerta_contra</b> vacío o NULL.
            </p>
        </div>

        {{-- ✅ SECCIÓN B: TABLA ALERTAS + BUSCADOR --}}
        <div class="bg-white  border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-300">📌 Alertas registradas (con texto)</div>
                    <div class="text-2xl font-bold text-red-600">{{ $totalAlertas ?? 0 }}</div>
                </div>

                <form method="GET" action="{{ route('contra.view') }}" class="w-full md:w-96">
                    <input
                        type="text"
                        name="q"
                        value="{{ $q ?? '' }}"
                        placeholder="Buscar (cédula / nombre / grado / alerta)..."
                        class="w-full border rounded-lg p-2 text-sm dark:bg-gray-900 dark:text-white dark:border-gray-700"
                    >
                </form>
            </div>

            <div class="overflow-auto mt-4">
                <table class="table-auto w-full text-sm border">
                    <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="px-2 py-1 border">Cédula</th>
                        <th class="px-2 py-1 border">Nombre</th>
                        <th class="px-2 py-1 border">Grado</th>
                        <th class="px-2 py-1 border">Alerta</th>
                        <th class="px-2 py-1 border">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($alertas as $u)
                        <tr>
                            <td class="border px-2 py-1">{{ $u->cedula }}</td>
                            <td class="border px-2 py-1">{{ $u->apellidos_nombres }}</td>
                            <td class="border px-2 py-1">{{ $u->grado }}</td>
                            <td class="border px-2 py-1 text-red-600 font-medium">
                                ⚠️ Alerta
                                <button
                                    type="button"
                                    class="ml-2 text-blue-600 underline"
                                    @click="alerta = @js($u->alerta_contra); open=true"
                                >
                                    Ver
                                </button>
                            </td>
                            <td class="border px-2 py-1">
                                <button
                                    type="button"
                                    class="text-blue-600 underline"
                                    @click="cedulaSeleccionada='{{ $u->cedula }}'; alerta=@js($u->alerta_contra); open=true"
                                >
                                    Editar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border px-2 py-4 text-center text-gray-500">
                                No existen alertas con texto para mostrar.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $alertas->links() }}
            </div>
        </div>

        {{-- ✅ MODAL (ver / editar) --}}
        <div
            x-show="open"
            x-cloak
            class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
            @keydown.escape.window="open=false"
            @click="open=false"
        >
            <div class="bg-white p-4 rounded shadow-md w-full max-w-lg" @click.stop>
                <h2 class="text-lg font-semibold text-red-600 mb-2">⚠️ Alerta</h2>

                <p class="text-gray-800 mb-3 whitespace-pre-line" x-text="alerta"></p>

                {{-- Editar alerta por cédula --}}
                <form action="{{ route('contra.guardarNovedad') }}" method="POST" class="space-y-2">
                    @csrf
                    <input type="hidden" name="cedula" :value="cedulaSeleccionada">

                    <label class="text-sm font-semibold">Editar / actualizar alerta:</label>
                    <textarea
                        name="novedad"
                        rows="3"
                        class="w-full border rounded p-2"
                        placeholder="Escriba la novedad (vacío = limpiar)"
                        x-model="alerta"
                    ></textarea>

                    <div class="flex gap-2 justify-end">
                        <button type="button" class="px-4 py-2 rounded bg-gray-200" @click="open=false">
                            Cerrar
                        </button>
                        <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
