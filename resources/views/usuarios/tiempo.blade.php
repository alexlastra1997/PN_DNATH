
<div class="flex flex-col w-full">               
    <div class="flex-1 bg-white rounded-lg shadow-xl p-8">
        <h4 class="text-xl text-gray-900 font-bold">Tiempo de Servicio</h4>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-4">
            <div class="px-6 py-6 bg-white  border-gray-300 rounded-lg shadow-2xl">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-sm text-indigo-600">Fecha de ingreso</span>
                </span>
                </div>
                <div class="flex items-center justify-between mt-6">
                    <div class="w-12 h-12 p-2.5 bg-indigo-400 bg-opacity-20 rounded-full text-indigo-600 border border-indigo-600">
                        <svg class="w-6 h-6 text-indigo-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                          </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-end">
                            <span class="text-2xl 2xl:text-3xl font-bold">{{ $usuario->fecha_ingreso }}</span>
                           
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-6 bg-white  border-gray-300 rounded-lg shadow-2xl">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-sm text-green-600">Fecha del pase anterior</span>
                    <span class="text-xs bg-gray-200 hover:bg-gray-500 text-gray-500 hover:text-gray-200 px-2 py-1 rounded-lg transition duration-200 cursor-default">{{ $usuario->tiempo_unidad_anterior }}</span>
                </div>
                <div class="flex items-center justify-between mt-6">
                    <div class="w-12 h-12 p-2.5 bg-green-400 bg-opacity-20 rounded-full text-green-600 border border-green-600">
                        <svg class="w-6 h-6 text-green-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                          </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-end">
                            <span class="text-2xl 2xl:text-3xl font-bold">{{ $usuario->fecha_pase_anterior }}</span>
                            <div class="flex items-center ml-2 mb-1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-6 bg-white  border-gray-300 rounded-lg shadow-2xl">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-sm text-blue-600">Fecha del pase actual</span>
                    <span class="text-xs bg-gray-200 hover:bg-gray-500 text-gray-500 hover:text-gray-200 px-2 py-1 rounded-lg transition duration-200 cursor-default">{{ $usuario->tiempo_ultimo_pase }}</span>
                </div>
                <div class="flex items-center justify-between mt-6">
                    <div>
                        <svg class="w-12 h-12 p-2.5 bg-blue-400 bg-opacity-20 rounded-full text-blue-600 border border-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-end">
                            <span class="text-2xl 2xl:text-3xl font-bold">{{ $usuario->fecha_pase_actual }}</span>
                            <div class="flex items-center ml-2 mb-1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



@php
    // Separar los títulos usando '|' como separador
    $historico_pases = explode('|', $usuario->historico_pases ?? '');
@endphp

@if (count($historico_pases) > 0 && $historico_pases[0] !== '')
    <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg overflow-hidden mt-4">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Historico de pases</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @foreach ($historico_pases as $item)
                @php
                    // Separar el título y la descripción por el separador '|'
                    $partes = explode('|', $item);
                    $titulo = trim($partes[0] ?? '');
                    $descripcion = trim($partes[1] ?? '');
                @endphp
                <tr>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $titulo }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-gray-500 mt-4">No hay títulos registrados.</p>
@endif



@php
    // Separar las capacitaciones usando '|' como separador
    $capacitaciones = explode('|', $usuario->capacitacion ?? '');
@endphp

@if (count($capacitaciones) > 0 && $capacitaciones[0] !== '')
    <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg overflow-hidden mt-4">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">País</th>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Capacitación</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @foreach ($capacitaciones as $item)
                @php
                    $partes = explode('--', $item);
                    $pais = trim($partes[0] ?? '');
                    $nombre = trim($partes[1] ?? '');
                @endphp
                <tr>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $pais }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $nombre }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-gray-500 mt-4">No hay capacitaciones registradas.</p>
@endif




@php
    // Asegurarse de que $designaciones sea un arreglo
    $usuario->designaciones = is_array($usuario->designaciones) ? $usuario->designaciones : explode('|', $usuario->designaciones);
@endphp

@if (count($usuario->designaciones) > 0)
    <div class="w-full overflow-x-auto mt-4 rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold uppercase whitespace-nowrap">Tipo de Designación</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold uppercase whitespace-nowrap">Desde</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold uppercase whitespace-nowrap">Hasta</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold uppercase whitespace-nowrap">País</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold uppercase whitespace-nowrap">Estado de Designación</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach ($usuario->designaciones as $item)
                    @php
                        // Separar la información por el delimitador '--'
                        $partes = explode('--', $item);

                        // Limpiar posibles espacios adicionales y asignar las partes a las variables
                        $tipo = trim($partes[0] ?? '');
                        $desde = trim($partes[1] ?? '');
                        $hasta = trim($partes[2] ?? '');
                        $pais = trim($partes[3] ?? '');
                        $estado = trim($partes[4] ?? '');
                    @endphp
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap">{{ $tipo }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap">{{ $desde }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap">{{ $hasta }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap">{{ $pais }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap">{{ $estado }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-gray-500 mt-4">No hay designaciones registradas.</p>
@endif



@php
    // Separar los títulos usando '|' como separador
    $titulos = explode('|', $usuario->titulos ?? '');
@endphp

@if (count($titulos) > 0 && $titulos[0] !== '')
    <div class="w-full overflow-x-auto mt-4 rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-600 text-white">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Título</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach ($titulos as $item)
                    @php
                        // Separar el título y la descripción por el separador '|'
                        $partes = explode('|', $item);
                        $titulo = trim($partes[0] ?? '');
                        $descripcion = trim($partes[1] ?? '');
                    @endphp
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap">{{ $titulo }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-gray-500 mt-4">No hay títulos registrados.</p>
@endif


@php
    // Separar los títulos usando '|' como separador
    $titulos_senescyt = explode('|', $usuario->titulos_senescyt ?? '');
@endphp

@if (count($titulos_senescyt) > 0 && $titulos_senescyt[0] !== '')
    <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg overflow-hidden mt-4">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Titulos Senescyt</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @foreach ($titulos_senescyt as $item)
                @php
                    // Separar el título y la descripción por el separador '|'
                    $partes = explode('|', $item);
                    $titulo = trim($partes[0] ?? '');
                    $descripcion = trim($partes[1] ?? '');
                @endphp
                <tr>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $titulo }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-gray-500 mt-4">No hay títulos registrados.</p>
@endif




@php
// Explota los méritos
$meritos = explode('|', $usuario->meritos ?? '');

// Limpia los méritos: quita espacios y vacíos
$meritos = array_filter(array_map('trim', $meritos));

// Cuenta cuántas veces se repite cada mérito
$meritosContados = array_count_values($meritos);
@endphp

@if (!empty($meritosContados))
<table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg overflow-hidden mt-4">
    <thead class="bg-blue-600 text-white">
        <tr>
            <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Mérito</th>
            <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Número de Méritos</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-100">
        @foreach ($meritosContados as $nombreMerito => $cantidad)
            <tr>
                <td class="px-4 py-2">{{ $nombreMerito }}</td>
                <td class="px-4 py-2">{{ $cantidad }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@else
<p class="text-gray-500 mt-4">No hay méritos registrados.</p>
@endif


<div class="bg-white rounded-lg shadow-md p-6 mt-8">
    <h4 class="text-xl font-bold text-gray-900 mb-4">Alertas del Servidor Policial</h4>

    <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg overflow-hidden">
        <thead class="bg-red-500 text-white">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Alerta</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @if (!empty($usuario->alertas))
                <tr>
                    <td class="px-4 py-2">{{ $usuario->alertas }}</td>
                </tr>
            @else
                <tr>
                    <td class="px-4 py-2 text-center text-gray-500">
                        El servidor policial no tiene alertas.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mt-8">
    <h4 class="text-xl font-bold text-gray-900 mb-4">Observaciones de Tenencia del Servidor Policial</h4>

    <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg overflow-hidden">
        <thead class="bg-red-600 text-white">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Observación</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @if (!empty($usuario->registra_observacion_tenencia))
                <tr>
                    <td class="px-4 py-2">{{ $usuario->registra_observacion_tenencia }}</td>
                </tr>
            @else
                <tr>
                    <td class="px-4 py-2 text-center text-gray-500">
                        El servidor policial no tiene observaciones registradas.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>


<div class="bg-white rounded-lg shadow-md p-6 mt-8">
    <h4 class="text-xl font-bold text-gray-900 mb-4">Contrato de Estudios del Servidor Policial</h4>

    <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg overflow-hidden">
        <thead class="bg-purple-600 text-white">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Contrato de Estudios</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @if (!empty($usuario->contrato_estudios))
                <tr>
                    <td class="px-4 py-2">{{ $usuario->contrato_estudios }}</td>
                </tr>
            @else
                <tr>
                    <td class="px-4 py-2 text-center text-gray-500">
                        El servidor policial no tiene contrato de estudios registrado.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mt-8">
    <h4 class="text-xl font-bold text-gray-900 mb-4">Información familiar sobre el Servidor Policial</h4>

    <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-lg overflow-hidden">
        <thead class="bg-indigo-600 text-white">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Conyuge Policía Activo</th>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Enfermedad Catastrófica (SP)</th>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Enfermedad Catastrófica (Cónyuge e Hijos)</th>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Enfermedad Catastrófica (Padres)</th>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Discapacidad (SP)</th>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Discapacidad (Cónyuge e Hijos)</th>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Discapacidad (Padres)</th>
                <th class="px-4 py-2 text-left text-sm font-semibold uppercase">Hijos Menores de 18</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            <tr>
                <td class="px-4 py-2">
                    {{ !empty($usuario->conyuge_policia_activo) ? $usuario->conyuge_policia_activo : 'No tiene información sobre cónyuge policía activo.' }}
                </td>
                <td class="px-4 py-2">
                    {{ !empty($usuario->enf_catast_sp) ? $usuario->enf_catast_sp : 'No tiene información sobre enfermedad catastrófica (SP).' }}
                </td>
                <td class="px-4 py-2">
                    {{ !empty($usuario->enf_catast_conyuge_hijos) ? $usuario->enf_catast_conyuge_hijos : 'No tiene información sobre enfermedad catastrófica (Cónyuge e Hijos).' }}
                </td>
                <td class="px-4 py-2">
                    {{ !empty($usuario->enf_catast_padres) ? $usuario->enf_catast_padres : 'No tiene información sobre enfermedad catastrófica (Padres).' }}
                </td>
                <td class="px-4 py-2">
                    {{ !empty($usuario->discapacidad_sp) ? $usuario->discapacidad_sp : 'No tiene información sobre discapacidad (SP).' }}
                </td>
                <td class="px-4 py-2">
                    {{ !empty($usuario->discapacidad_conyuge_hijos) ? $usuario->discapacidad_conyuge_hijos : 'No tiene información sobre discapacidad (Cónyuge e Hijos).' }}
                </td>
                <td class="px-4 py-2">
                    {{ !empty($usuario->discapacidad_padres) ? $usuario->discapacidad_padres : 'No tiene información sobre discapacidad (Padres).' }}
                </td>
                <td class="px-4 py-2">
                    {{ !empty($usuario->hijos_menor_igual_18) ? $usuario->hijos_menor_igual_18 : 'No tiene información sobre hijos menores de 18 años.' }}
                </td>
            </tr>
        </tbody>
    </table>
</div>



