@extends('layouts.app')

@section('content')
    <div class="p-4 max-w-screen-2xl mx-auto dark:text-white">

        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
            Ocupantes de {{ $nomenclatura }} — {{ $cargo }}
        </h2>

        {{-- ====================== TABLA INFORMATIVA DEL CARGO ====================== --}}
        <div class="mb-6 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                    Información del cargo en el Orgánico
                </h3>
                <p class="text-xs mt-1 text-gray-600 dark:text-white">
                    Fuente: <code class="font-mono">reporte_organico</code>
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-100 dark:bg-gray-900/30">
                    <tr class="text-left">
                        <th class="px-4 py-2 font-semibold">Servicio</th>
                        <th class="px-4 py-2 font-semibold">Nomenclatura</th>
                        <th class="px-4 py-2 font-semibold">Cargo</th>
                        <th class="px-4 py-2 font-semibold">Grado(s) Orgánico(s)</th>
                        <th class="px-4 py-2 font-semibold">Personal Orgánico</th>
                        <th class="px-4 py-2 font-semibold">N° Orgánico Ideal</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($infoCargo as $fila)
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2">{{ $fila->servicio_organico }}</td>
                            <td class="px-4 py-2">{{ $fila->nomenclatura_organico }}</td>
                            <td class="px-4 py-2">{{ $fila->cargo_organico }}</td>
                            <td class="px-4 py-2">{{ $fila->grado_organico }}</td>
                            <td class="px-4 py-2">{{ $fila->personal_organico }}</td>
                            <td class="px-4 py-2">{{ $fila->numero_organico_ideal }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center text-gray-500 dark:text-white">
                                No hay registros en <code class="font-mono">reporte_organico</code> para esta combinación.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @php
            // Normaliza arreglo de grados permitidos
            $permitidos  = array_map(fn($g) => mb_strtoupper(trim($g)), $gradosPermitidos ?? []);
            $tieneReglas = !empty($permitidos);
            // ¿Hay al menos un ocupante que NO cumpla?
            $hayAlertas = $tieneReglas && collect($ocupantes)->contains(function($o) use ($permitidos) {
                $g = mb_strtoupper(trim($o->grado ?? ''));
                return $g === '' || !in_array($g, $permitidos, true);
            });
        @endphp

        {{-- Leyenda de alerta: SOLO si existe al menos un caso que no cumple --}}
        @if($hayAlertas)
            <div class="mb-3">
        <span class="inline-flex items-center gap-2 text-xs rounded-md px-2.5 py-1.5 border
                     border-red-300 bg-red-50 text-red-700
                     dark:border-red-600 dark:bg-red-900/30 dark:text-red-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.721-1.36 3.486 0l6.518 11.588c.75 1.334-.213 2.993-1.743 2.993H3.482c-1.53 0-2.493-1.659-1.743-2.993L8.257 3.1zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V8a1 1 0 112 0v3a1 1 0 01-1 1z" clip-rule="evenodd"/>
            </svg>
            <span>⚠️ Indica servidor que <strong>no cumple</strong> con el grado orgánico.</span>
        </span>
            </div>
        @endif

        {{-- =========================== TABLA DE OCUPANTES =========================== --}}
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Ocupantes actuales</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-100 dark:bg-gray-900/30">
                    <tr class="text-left">
                        <th class="px-4 py-2 font-semibold">Cédula</th>
                        <th class="px-4 py-2 font-semibold">Grado</th>
                        <th class="px-4 py-2 font-semibold">Apellidos y Nombres</th>
                        <th class="px-4 py-2 font-semibold">Estado de Traslado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($ocupantes as $ocupante)
                        @php
                            $gradoOcupante = mb_strtoupper(trim($ocupante->grado ?? ''));
                            $cumpleGrado   = $tieneReglas && $gradoOcupante !== '' && in_array($gradoOcupante, $permitidos, true);
                        @endphp
                        <tr class="border-t border-gray-200 dark:border-gray-700 @if($tieneReglas && !$cumpleGrado) bg-red-50 dark:bg-red-900/20 @endif">
                            <td class="px-4 py-2">{{ $ocupante->cedula }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center gap-1">
                                    <span>{{ $ocupante->grado }}</span>
                                    @if($tieneReglas && !$cumpleGrado)
                                        <span class="sr-only">No cumple con el grado orgánico</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600 dark:text-red-400"
                                             viewBox="0 0 20 20" fill="currentColor" title="No cumple con el grado orgánico">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.721-1.36 3.486 0l6.518 11.588c.75 1.334-.213 2.993-1.743 2.993H3.482c-1.53 0-2.493-1.659-1.743-2.993L8.257 3.1zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V8a1 1 0 112 0v3a1 1 0 01-1 1z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ $ocupante->apellidos_nombres }}</td>
                            <td class="px-4 py-2">{{ $ocupante->estado_efectivo }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-center text-gray-500 dark:text-white">
                                No hay ocupantes.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
