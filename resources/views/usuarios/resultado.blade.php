@extends('layouts.app')

@section('content')
<div class="p-4 space-y-6">
    <h2 class="text-xl font-bold">Resultados</h2>

    {{-- Filtros --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
       

        <select name="alerta" class="filtro border p-1 rounded">
            <option value="">¿Tiene alerta?</option>
            <option value="SI">SI</option>
            <option value="NO">NO</option>
        </select>

        <select name="estado_civil" class="filtro border p-1 rounded">
            <option value="">Estado Civil</option>
            @foreach($estados_civiles as $ec)
                <option value="{{ $ec }}">{{ $ec }}</option>
            @endforeach
        </select>

        <select name="sitio_priorizado" class="filtro border p-1 rounded">
            <option value="">¿Sitio Priorizado?</option>
            <option value="SI">SI</option>
            <option value="NO">NO</option>
        </select>

        @foreach(['contrato_estudios', 'conyuge_policia_activo', 'enf_catast_sp', 'enf_catast_conyuge_hijos', 'discapacidad_sp', 'discapacidad_conyuge_hijos'] as $campo)
            <select name="{{ $campo }}" class="filtro border p-1 rounded">
                <option value="">{{ ucwords(str_replace('_', ' ', $campo)) }}</option>
                <option value="SI">SI</option>
                <option value="NO">NO</option>
            </select>
        @endforeach

        <div class="col-span-2">
            <label for="promocion" class="block text-sm font-bold mb-1">Promoción</label>
            <select name="promocion[]" id="promocion" multiple class="filtro border p-1 rounded w-full">
                @foreach($promociones as $promo)
                    <option value="{{ $promo }}">{{ $promo }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-span-2">
            <label for="provincia_trabaja" class="block text-sm font-bold mb-1">Provincias (trabaja)</label>
            <select name="provincia_trabaja[]" id="provincia_trabaja" multiple class="filtro border p-1 rounded w-full">
                @foreach(['AZUAY', 'BOLIVAR', 'CAÑAR', 'CARCHI', 'CHIMBORAZO', 'COTOPAXI', 'EL ORO',
                          'ESMERALDAS', 'GALAPAGOS', 'GUAYAS', 'IMBABURA', 'LOJA', 'LOS RIOS', 'MANABI',
                          'MORONA SANTIAGO', 'NAPO', 'ORELLANA', 'PASTAZA', 'PICHINCHA', 'SANTA ELENA',
                          'STO DOMINGO TSACHILAS', 'SUCUMBIOS', 'TUNGURAHUA', 'ZAMORA CHINCHIPE'] as $prov)
                    <option value="{{ $prov }}">{{ $prov }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-span-2 flex space-x-4">
            <button id="limpiar-filtros" class="bg-gray-200 text-black px-3 py-1 rounded hover:bg-gray-300 text-sm">
                Limpiar filtros
            </button>

            <form method="POST" action="{{ route('usuarios.descargar_excel') }}" id="form-descargar">
                @csrf
                <input type="hidden" name="cedulas" id="cedulas_export">
                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
                    Descargar Excel Traslado Masivo
                </button>
            </form>
        </div>
    </div>

    {{-- Tabla APTOS --}}
    <div id="tabla-aptos" class="mt-6">
        @include('usuarios.partials.tabla_aptos', ['aptos' => $usuarios])
    </div>

    {{-- Tabla NO APTOS --}}
    <hr class="my-6 border-gray-400">
    <h3 class="text-red-600 font-bold text-lg">NO APTOS</h3>
    <div id="tabla-no-aptos"></div>
</div>

{{-- Tom Select --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<script>
    const filtros = document.querySelectorAll('.filtro');
    const cedulas = @json($usuarios->pluck('cedula')->toArray());

    new TomSelect('#promocion', {
        plugins: ['remove_button'],
        maxOptions: 2000,
        placeholder: 'Selecciona promociones...'
    });

    new TomSelect('#provincia_trabaja', {
        plugins: ['remove_button'],
        maxOptions: 100,
        placeholder: 'Selecciona provincias...'
    });

    filtros.forEach(f => {
        f.addEventListener('change', () => {
            const data = {
                cedulas_filtradas: cedulas.join(','),
                provincia_trabaja: Array.from(document.querySelector('[name="provincia_trabaja[]"]').selectedOptions).map(opt => opt.value),
                alerta: document.querySelector('[name="alerta"]').value,
                estado_civil: document.querySelector('[name="estado_civil"]').value,
                sitio_priorizado: document.querySelector('[name="sitio_priorizado"]').value,
                contrato_estudios: document.querySelector('[name="contrato_estudios"]').value,
                conyuge_policia_activo: document.querySelector('[name="conyuge_policia_activo"]').value,
                enf_catast_sp: document.querySelector('[name="enf_catast_sp"]').value,
                enf_catast_conyuge_hijos: document.querySelector('[name="enf_catast_conyuge_hijos"]').value,
                discapacidad_sp: document.querySelector('[name="discapacidad_sp"]').value,
                discapacidad_conyuge_hijos: document.querySelector('[name="discapacidad_conyuge_hijos"]').value,
                promocion: Array.from(document.querySelector('[name="promocion[]"]').selectedOptions).map(opt => opt.value),
                _token: '{{ csrf_token() }}'
            };

            fetch("{{ route('usuarios.filtrar.ajax') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                document.getElementById('tabla-aptos').innerHTML = res.aptos;
                document.getElementById('tabla-no-aptos').innerHTML = res.no_aptos;
                document.getElementById('cedulas_export').value = data.cedulas_filtradas;
            });
        });
    });

    document.getElementById('limpiar-filtros').addEventListener('click', () => {
        document.querySelectorAll('.filtro').forEach(input => {
            if (input.tagName === 'SELECT' && !input.multiple) {
                input.selectedIndex = 0;
            }
            if (input.multiple) {
                Array.from(input.options).forEach(opt => opt.selected = false);
            }
        });

        const data = {
            cedulas_filtradas: cedulas.join(','),
            _token: '{{ csrf_token() }}'
        };

        fetch("{{ route('usuarios.filtrar.ajax') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            document.getElementById('tabla-aptos').innerHTML = res.aptos;
            document.getElementById('tabla-no-aptos').innerHTML = '';
            document.getElementById('cedulas_export').value = data.cedulas_filtradas;
        });
    });
</script>
@endsection
