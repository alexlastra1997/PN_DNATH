@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Usuarios encontrados</h2>

    @if($usuarios->count())

        <!-- FORMULARIO DE FILTRO CON BOTÓN -->
        <form action="{{ route('usuarios.filtrarProvincia') }}" method="POST" class="mb-4 border p-4 rounded shadow space-y-2">
            @csrf
            <input type="hidden" name="cedulas_filtradas" value="{{ implode(',', $usuarios->pluck('cedula')->toArray()) }}">

             <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <!-- provincia -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Provincia</label>
            <input type="text" name="provincia_trabaja" value="{{ request('provincia_trabaja') }}" class="mt-1 block w-full border rounded p-2 text-sm">
        </div>

        <!-- alertas -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Alertas</label>
            <select name="alerta" class="mt-1 block w-full border rounded p-2 text-sm">
                <option value="">-- Todos --</option>
                <option value="SI" {{ request('alerta') == 'SI' ? 'selected' : '' }}>SI</option>
                <option value="NO" {{ request('alerta') == 'NO' ? 'selected' : '' }}>NO</option>
            </select>
        </div>

        <!-- contrato_estudios -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Contrato de estudios</label>
            <select name="contrato_estudios" class="mt-1 block w-full border rounded p-2 text-sm">
                <option value="">-- Todos --</option>
                <option value="SI" {{ request('contrato_estudios') == 'SI' ? 'selected' : '' }}>SI</option>
                <option value="NO" {{ request('contrato_estudios') == 'NO' ? 'selected' : '' }}>NO</option>
            </select>
        </div>

        <!-- conyuge_policia_activo -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Cónyuge policía activo</label>
            <select name="conyuge_policia_activo" class="mt-1 block w-full border rounded p-2 text-sm">
                <option value="">-- Todos --</option>
                <option value="SI" {{ request('conyuge_policia_activo') == 'SI' ? 'selected' : '' }}>SI</option>
                <option value="NO" {{ request('conyuge_policia_activo') == 'NO' ? 'selected' : '' }}>NO</option>
            </select>
        </div>

        <!-- enf_catast_sp -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Enf. catastrófica SP</label>
            <select name="enf_catast_sp" class="mt-1 block w-full border rounded p-2 text-sm">
                <option value="">-- Todos --</option>
                <option value="SI" {{ request('enf_catast_sp') == 'SI' ? 'selected' : '' }}>SI</option>
                <option value="NO" {{ request('enf_catast_sp') == 'NO' ? 'selected' : '' }}>NO</option>
            </select>
        </div>

        <!-- enf_catast_conyuge_hijos -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Enf. catastrófica cónyuge/hijos</label>
            <select name="enf_catast_conyuge_hijos" class="mt-1 block w-full border rounded p-2 text-sm">
                <option value="">-- Todos --</option>
                <option value="SI" {{ request('enf_catast_conyuge_hijos') == 'SI' ? 'selected' : '' }}>SI</option>
                <option value="NO" {{ request('enf_catast_conyuge_hijos') == 'NO' ? 'selected' : '' }}>NO</option>
            </select>
        </div>

        <!-- discapacidad_sp -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Discapacidad SP</label>
            <select name="discapacidad_sp" class="mt-1 block w-full border rounded p-2 text-sm">
                <option value="">-- Todos --</option>
                <option value="SI" {{ request('discapacidad_sp') == 'SI' ? 'selected' : '' }}>SI</option>
                <option value="NO" {{ request('discapacidad_sp') == 'NO' ? 'selected' : '' }}>NO</option>
            </select>
        </div>

        <!-- discapacidad_conyuge_hijos -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Discapacidad cónyuge/hijos</label>
            <select name="discapacidad_conyuge_hijos" class="mt-1 block w-full border rounded p-2 text-sm">
                <option value="">-- Todos --</option>
                <option value="SI" {{ request('discapacidad_conyuge_hijos') == 'SI' ? 'selected' : '' }}>SI</option>
                <option value="NO" {{ request('discapacidad_conyuge_hijos') == 'NO' ? 'selected' : '' }}>NO</option>
            </select>
        </div>

        <!-- estado_civil -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Estado civil</label>
            <select name="estado_civil" class="mt-1 block w-full border rounded p-2 text-sm">
                <option value="">-- Todos --</option>
                @foreach($estados_civiles as $estado)
                    <option value="{{ $estado }}" {{ request('estado_civil') == $estado ? 'selected' : '' }}>{{ $estado }}</option>
                @endforeach
            </select>
        </div>

        <!-- promocion (multi) -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Promoción</label>
            <select name="promocion[]" multiple class="mt-1 block w-full border rounded p-2 text-sm">
                @foreach($promociones as $promo)
                    <option value="{{ $promo }}" {{ collect(request('promocion'))->contains($promo) ? 'selected' : '' }}>
                        {{ $promo }}
                    </option>
                @endforeach
            </select>
            <small class="text-xs text-gray-500">Usa Ctrl o Cmd para elegir varias opciones</small>
        </div>

        <!-- sitio priorizado -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Sitio priorizado</label>
            <select name="sitio_priorizado" class="mt-1 block w-full border rounded p-2 text-sm">
                <option value="">-- Todos --</option>
                <option value="SI" {{ request('sitio_priorizado') == 'SI' ? 'selected' : '' }}>SI</option>
                <option value="NO" {{ request('sitio_priorizado') == 'NO' ? 'selected' : '' }}>NO</option>
            </select>
        </div>

    </div>

    <div class="mt-4 flex justify-end">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
            Filtrar
        </button>
    </div>
        </form>
        
        <!-- TABLA DE RESULTADOS -->
        <table class="min-w-full border text-xs">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-2 py-1">Cédula</th>
                    <th class="border px-2 py-1">Grado</th>
                    <th class="border px-2 py-1">Nombre</th>
                    <th class="border px-2 py-1">Unidad</th>
                    <th class="border px-2 py-1">Función</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                    <tr>
                        <td class="border px-2 py-1">{{ $usuario->cedula }}</td>
                        <td class="border px-2 py-1">{{ $usuario->grado}}</td>
                        <td class="border px-2 py-1">{{ $usuario->apellidos_nombres }}</td>
                        <td class="border px-2 py-1">{{ $usuario->nomenclatura_territorio_efectivo }}</td>
                        <td class="border px-2 py-1">{{ $usuario->descFuncion_territorio_efectivo}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-red-500">No se encontraron usuarios con las cédulas proporcionadas.</p>
    @endif

    <a href="{{ route('opciones') }}" class="mt-4 inline-block bg-gray-500 text-white px-4 py-2 rounded">Regresar</a>
</div>
@endsection
