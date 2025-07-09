@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">Editar Información</h1>

<form action="{{ route('detha.update', $usuario->id) }}" method="POST" class="space-y-4">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- DIRECCIÓN UNIDAD ZONA POLICIA --}}
        <div>
            <label class="block font-semibold mb-1">Dirección Unidad Zona Policía</label>
            <select name="direccion_unidad_zona_policia" class="border p-2 w-full rounded">
                <option value="">-- Seleccionar --</option>
                <option value="COMANDO-GENERAL" {{ $usuario->direccion_unidad_zona_policia == 'COMANDO-GENERAL' ? 'selected' : '' }}>COMANDO-GENERAL</option>
                <option value="SUBCOMANDO-GENERAL" {{ $usuario->direccion_unidad_zona_policia == 'SUBCOMANDO-GENERAL' ? 'selected' : '' }}>SUBCOMANDO-GENERAL</option>
                <!-- agrega más opciones si gustas -->
            </select>
        </div>

        {{-- SUB ZONA DE POLICÍA --}}
        <div>
            <label class="block font-semibold mb-1">Sub Zona de Policía</label>
            <select name="sub_zona_policia" class="border p-2 w-full rounded">
                <option value="">-- Seleccionar --</option>
                @foreach(['AZUAY','BOLIVAR','CAÑAR','CARCHI','CHIMBORAZO','COTOPAXI','GUAYAS','PICHINCHA'] as $subzona)
                    <option value="{{ $subzona }}" {{ $usuario->sub_zona_policia == $subzona ? 'selected' : '' }}>{{ $subzona }}</option>
                @endforeach
            </select>
        </div>

        {{-- DISTRITO --}}
        <div>
            <label class="block font-semibold mb-1">Distrito</label>
            <select name="distrito" class="border p-2 w-full rounded">
                <option value="">-- Seleccionar --</option>
                @foreach(['D-9 DE OCTUBRE','D-24 MAYO','D-AMBATO SUR','D-QUITO','D-GUAYAQUIL'] as $distrito)
                    <option value="{{ $distrito }}" {{ $usuario->distrito == $distrito ? 'selected' : '' }}>{{ $distrito }}</option>
                @endforeach
            </select>
        </div>

        {{-- CIRCUITO DEPARTAMENTO SECCION --}}
        <div>
            <label class="block font-semibold mb-1">Circuito / Departamento / Sección</label>
            <input type="text" name="circuito_departamento_seccion" value="{{ $usuario->circuito_departamento_seccion }}" class="border p-2 w-full rounded">
        </div>

        {{-- SUBCIRCUITO --}}
        <div>
            <label class="block font-semibold mb-1">Subcircuito</label>
            <input type="text" name="subcircuito" value="{{ $usuario->subcircuito }}" class="border p-2 w-full rounded">
        </div>

        {{-- FUNCIÓN ASIGNADA --}}
        <div>
            <label class="block font-semibold mb-1">Función Asignada</label>
            <input type="text" name="funcion_asignada" value="{{ $usuario->funcion_asignada }}" class="border p-2 w-full rounded">
        </div>

        {{-- FECHA DE PRESENTACION NUEVA --}}
        <div>
            <label class="block font-semibold mb-1">Fecha de Presentación Nueva</label>
            <input type="date" name="fecha_presentacion_nueva" value="{{ $usuario->fecha_presentacion_nueva }}" class="border p-2 w-full rounded">
        </div>

        {{-- NOVEDAD --}}
        <div>
            <label class="block font-semibold mb-1">Novedad</label>
            <input type="text" name="novedad" value="{{ $usuario->novedad }}" class="border p-2 w-full rounded">
        </div>

        {{-- DEPENDENCIA DESTINO --}}
        <div>
            <label class="block font-semibold mb-1">Dependencia Destino</label>
            <input type="text" name="dependencia_destino" value="{{ $usuario->dependencia_destino }}" class="border p-2 w-full rounded">
        </div>

        {{-- DETALLE NOVEDAD NUEVA UNIDAD --}}
        <div>
            <label class="block font-semibold mb-1">Detalle Novedad Nueva Unidad</label>
            <input type="text" name="detalle_novedad_nueva_unidad" value="{{ $usuario->detalle_novedad_nueva_unidad }}" class="border p-2 w-full rounded">
        </div>

        {{-- FECHA NOVEDAD --}}
        <div>
            <label class="block font-semibold mb-1">Fecha de la Novedad</label>
            <input type="date" name="fecha_novedad" value="{{ $usuario->fecha_novedad }}" class="border p-2 w-full rounded">
        </div>

        {{-- TIPO DOCUMENTO --}}
        <div>
            <label class="block font-semibold mb-1">Tipo Documento</label>
            <input type="text" name="tipo_documento" value="{{ $usuario->tipo_documento }}" class="border p-2 w-full rounded">
        </div>

        {{-- DOCUMENTO REFERENCIA --}}
        <div>
            <label class="block font-semibold mb-1">Documento Referencia</label>
            <input type="text" name="documento_referencia" value="{{ $usuario->documento_referencia }}" class="border p-2 w-full rounded">
        </div>

        {{-- N° GRUPO DE TRABAJO --}}
        <div>
            <label class="block font-semibold mb-1">Número Grupo Trabajo</label>
            <input type="text" name="numero_grupo_trabajo" value="{{ $usuario->numero_grupo_trabajo }}" class="border p-2 w-full rounded">
        </div>

        {{-- GRUPO --}}
        <div>
            <label class="block font-semibold mb-1">Grupo</label>
            <select name="grupo" class="border p-2 w-full rounded">
                <option value="">-- Seleccionar --</option>
                @for($i=1;$i<=4;$i++)
                    <option value="{{ $i }}" {{ $usuario->grupo == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>

        {{-- MODALIDAD --}}
        <div>
            <label class="block font-semibold mb-1">Modalidad</label>
            <select name="modalidad" class="border p-2 w-full rounded">
                <option value="">-- Seleccionar --</option>
                <option value="9-3" {{ $usuario->modalidad == '9-3' ? 'selected' : '' }}>9-3</option>
                <option value="11-3" {{ $usuario->modalidad == '11-3' ? 'selected' : '' }}>11-3</option>
            </select>
        </div>

        {{-- TIPO SANGRE --}}
        <div>
            <label class="block font-semibold mb-1">Tipo Sangre</label>
            <select name="tipo_sangre" class="border p-2 w-full rounded">
                <option value="">-- Seleccionar --</option>
                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $tipo)
                    <option value="{{ $tipo }}" {{ $usuario->tipo_sangre == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                @endforeach
            </select>
        </div>

        {{-- LICENCIA CONDUCIR --}}
        <div>
            <label class="block font-semibold mb-1">Licencia de Conducir</label>
            <select name="licencia_conducir" class="border p-2 w-full rounded">
                <option value="">-- Seleccionar --</option>
                <option value="SI" {{ $usuario->licencia_conducir == 'SI' ? 'selected' : '' }}>SI</option>
                <option value="NO" {{ $usuario->licencia_conducir == 'NO' ? 'selected' : '' }}>NO</option>
            </select>
        </div>

        {{-- CORREO ELECTRONICO --}}
        <div>
            <label class="block font-semibold mb-1">Correo Electrónico</label>
            <input type="email" name="correo_electronico" value="{{ $usuario->correo_electronico }}" class="border p-2 w-full rounded">
        </div>

        {{-- NÚMERO CELULAR --}}
        <div>
            <label class="block font-semibold mb-1">Número Celular</label>
            <input type="text" name="numero_celular" value="{{ $usuario->numero_celular }}" class="border p-2 w-full rounded">
        </div>

        {{-- NÚMERO CELULAR FAMILIAR --}}
        <div>
            <label class="block font-semibold mb-1">Número Celular Familiar</label>
            <input type="text" name="numero_celular_familiar" value="{{ $usuario->numero_celular_familiar }}" class="border p-2 w-full rounded">
        </div>

    </div>

    <div class="mt-6 space-x-2">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Guardar</button>
        <a href="{{ route('detha.show', $usuario->id) }}" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</a>
    </div>
</form>
@endsection
