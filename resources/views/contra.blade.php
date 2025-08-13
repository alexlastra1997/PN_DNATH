@extends('layouts.app')

@section('content')
<!-- Contenedor Alpine.js -->
<div x-data="{ open: false, alerta: '' }" class="max-w-5xl mx-auto p-4">

    <h1 class="text-xl font-bold mb-4">📥 Importar Cédulas para Alertas</h1>

    {{-- Formulario para subir archivo --}}
    <form action="{{ route('contra.import') }}" method="POST" enctype="multipart/form-data" class="mb-6">
        @csrf
        <input type="file" name="file" required class="border p-2 rounded">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded ml-2">Subir Excel</button>
    </form>

    {{-- Mostrar usuarios importados --}}
    @if(session('usuarios') && count(session('usuarios')) > 0)
        <div class="overflow-auto mb-6">
            <table class="table-auto w-full text-sm border">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="px-2 py-1 border">Cédula</th>
                        <th class="px-2 py-1 border">Nombre</th>
                        <th class="px-2 py-1 border">Grado</th>
                        <th class="px-2 py-1 border">Alerta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (session('usuarios') as $u)
                        <tr>
                            <td class="border px-2 py-1">{{ $u->cedula }}</td>
                            <td class="border px-2 py-1">{{ $u->apellidos_nombres }}</td>
                            <td class="border px-2 py-1">{{ $u->grado }}</td>
                            <td class="border px-2 py-1 text-red-600 font-medium">
                                @if (!empty($u->alerta_contra) && $u->alerta_contra !== '⚠️')
                                    ⚠️ Alerta
                                    <button
                                        type="button"
                                        class="ml-2 text-blue-600 underline"
                                        @click="alerta = '{{ addslashes($u->alerta_contra) }}'; open = true"
                                    >
                                        Ver Alerta
                                    </button>
                                @elseif ($u->alerta_contra === '⚠️')
                                    ⚠️
                                @else
                                    –
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Formulario para guardar novedad --}}
        <form action="{{ route('contra.guardarNovedad') }}" method="POST" class="mb-4">
            @csrf
            <input type="hidden" name="cedulas" value="{{ implode(',', session('usuarios')->pluck('cedula')->toArray()) }}">
            <label for="novedad" class="block font-semibold mb-1">Escriba la novedad (opcional para limpiar):</label>
            <textarea name="novedad" id="novedad" rows="3" class="w-full border rounded p-2 mb-2" placeholder="Ej: Enviado a Contraloría..."></textarea>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Guardar Novedad</button>
        </form>
    @endif

    {{-- Mensajes de éxito --}}
    @if(session('success'))
        <div class="mt-4 p-2 bg-green-200 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Popup Modal de Alerta --}}
    <div
        x-show="open"
        x-transition
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
        <div class="bg-white p-4 rounded shadow-md w-96">
            <h2 class="text-lg font-semibold text-red-600 mb-2">⚠️ Alerta</h2>
            <p class="text-gray-800 mb-4" x-text="alerta"></p>
            <button @click="open = false" class="bg-blue-600 text-white px-4 py-2 rounded">Cerrar</button>
        </div>
    </div>
</div>
@endsection
