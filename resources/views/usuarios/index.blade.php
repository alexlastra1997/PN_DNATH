{{-- resources/views/usuarios/index.blade.php --}}
@extends('layouts.app')

@section('content')

    <section class="bg-gray-50 dark:  sm:py-5">
        <div class="px-4 mx-auto max-w-screen-2xl lg:px-12">
            <div class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
                <div class="flex flex-col px-4 py-3 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
                    
                    <form action="#" method="GET" class="hidden md:block md:pl-2">
                        <label for="topbar-search" class="sr-only">Search</label>
                        <div class="relative md:w-96">
                            <div
                                class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                                <svg
                                class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                                >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                ></path>
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="search" value="{{ request('search') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Search"
                            />
                        </div>
                    </form>

                    <div class="flex items-center justify-between w-full lg:w-auto">
                            <!-- drawer init and show -->
                        <div class="text-center m-5 ">
                            <button id="readProductButton"
                                class="flex items-center gap-2 text-white bg-blue-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800"
                                type="button"
                                data-drawer-target="readProductDrawer"
                                data-drawer-show="readProductDrawer"
                                aria-controls="readProductDrawer"
                            >
                                <svg class="w-[21px] h-[21px] text-white" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M5.05 3C3.291 3 2.352 5.024 3.51 6.317l5.422 6.059v4.874c0 .472.227.917.613 1.2l3.069 2.25c1.01.742 2.454.036 2.454-1.2v-7.124l5.422-6.059C21.647 5.024 20.708 3 18.95 3H5.05Z"/>
                                </svg>
                                <span>Filtrar</span>
                            </button>
                        </div>
                        
                        <!-- drawer component -->
                        <div id="readProductDrawer" class="fixed top-20 sm:top-12 left-0 z-40 w-full max-w-2xl h-screen bg-white transition-transform -translate-x-full dark:bg-gray-800" tabindex="-1" aria-labelledby="drawer-label" aria-hidden="true">
                            <div class="h-full overflow-y-auto lg:overflow-hidden p-4">        
                                <div>
                                    <h4 id="drawer-label" class="mb-6 leading-none text-xl font-semibold text-gray-900 dark:text-white">FILTRAR</h4>  
                                </div>
                        
                                <button type="button" data-drawer-dismiss="readProductDrawer" aria-controls="readProductDrawer" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 absolute top-2.5 right-2.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                    <span class="sr-only">Close menu</span>
                                </button>
                        
                                <form method="GET" action="{{ route('usuarios.index') }}" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                                    <div class="grid grid-cols-1 gap-4">
                                        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                                </svg>
                                            </div>
                                            <input type="text" name="search" value="{{ request('search') }}" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Mockups, Logos..."  />
                                        </div>
                                    </div>
                        
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Grado</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2 max-h-64 overflow-y-auto p-2 border border-gray-300 rounded-md">
                                            @foreach($grado as $g)
                                                <label class="flex items-center space-x-2 text-sm text-gray-700 dark:text-gray-200">
                                                    <input type="checkbox" name="grado[]" value="{{ $g }}" 
                                                        {{ is_array(request('grado')) && in_array($g, request('grado')) ? 'checked' : '' }}
                                                        class="text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                    <span>{{ $g }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                        
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                        <div>
                                            <label for="sexo" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Sexo</label>
                                            <select name="sexo" id="sexo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">>
                                                <option value="">-- Todos --</option>
                                                @foreach($sexo as $sexo_selected)
                                                    <option value="{{ $sexo_selected }}" {{ request('sexo') == $sexo_selected ? 'selected' : '' }}>
                                                        {{ $sexo_selected }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label for="hijos18" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Número de hijos</label>
                                            <select name="hijos18" id="hijos18" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">>
                                                <option value="">-- Todos --</option>
                                                @foreach($hijos18 as $hijos)
                                                    <option value="{{ $hijos }}" {{ request('hijos18') == $hijos ? 'selected' : '' }}>
                                                        {{ $hijos }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                        
                                        <div>
                                            <label for="tipo_personal" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tipo de Personal</label>
                                            <select name="tipo_personal" id="tipo_personal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">>
                                                <option value="">-- Todos --</option>
                                                <option value="A" {{ request('tipo_personal') == 'L' ? 'selected' : '' }}>Administrativo (A)</option>
                                                <option value="J" {{ request('tipo_personal') == 'L' ? 'selected' : '' }}>Justicia (J)</option>
                                                <option value="I" {{ request('tipo_personal') == 'L' ? 'selected' : '' }}>Intendencia (I)</option>
                                                <option value="S" {{ request('tipo_personal') == 'L' ? 'selected' : '' }}>Sanidad (S)</option>
                                                <option value="L" {{ request('tipo_personal') == 'L' ? 'selected' : '' }}>Línea (L)</option>
                                                <option value="S" {{ request('tipo_personal') == 'S' ? 'selected' : '' }}>Servicio (S)</option>
                                                <option value="T" {{ request('tipo_personal') == 'L' ? 'selected' : '' }}>Transporte (T)</option>
                        
                                            </select>
                                        </div>
                        
                                        <div>
                                            <label for="estado_civil" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Estado Civil</label>
                                            <select name="estado_civil" id="estado_civil" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">>
                                                <option value="">-- Todos --</option>
                                                @foreach($estado_civil as $estado)
                                                    <option value="{{ $estado }}" {{ request('estado_civil') == $estado ? 'selected' : '' }}>
                                                        {{ $estado }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                        
                                        <div>
                                            <label for="unidad" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Unidad</label>
                                            <select name="unidad" id="unidad" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">>
                                                <option value="">-- Todos --</option>
                                                @foreach($unidad as $uni)
                                                    <option value="{{ $uni }}" {{ request('estado_civil') == $uni ? 'selected' : '' }}>
                                                        {{ $uni }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                        
                                        <div>
                                            <label for="cuadro_policial" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Cuadro Policial</label>
                                            <select name="cuadro_policial" id="cuadro_policial" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">>
                                                <option value="">-- Todos --</option>
                                                @foreach($cuadro_policial as $cuadro)
                                                    <option value="{{ $cuadro }}" {{ request('cuadro_policial') == $cuadro ? 'selected' : '' }}>
                                                        {{ $cuadro }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                        
                                        <div>
                                            <label for="cdg_promocion" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Promoción</label>
                                            <select name="cdg_promocion" id="cdg_promocion" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">>
                                                <option value="">-- Todos --</option>
                                                @foreach($cdg_promocion as $promo)
                                                    <option value="{{ $promo }}" {{ request('cdg_promocion') == $promo ? 'selected' : '' }}>
                                                        {{ $promo }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                        
                                        <div>
                                            <label for="provincia_trabaja" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Provincia en la que trabaja</label>
                                            <select name="provincia_trabaja" id="provincia_trabaja" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">>
                                                <option value="">-- Todos --</option>
                                                @foreach($provincia_trabaja as $tra)
                                                    <option value="{{ $tra }}" {{ request('provincia_trabaja') == $tra ? 'selected' : '' }}>
                                                        {{ $tra }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                        
                                        <div>
                                            <label for="provincia_vive" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Provincia en la que vive</label>
                                            <select name="provincia_vive" id="provincia_vive" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">>
                                                <option value="">-- Todos --</option>
                                                @foreach($provincia_vive as $vive)
                                                    <option value="{{ $vive }}" {{ request('provincia_vive') == $vive ? 'selected' : '' }}>
                                                        {{ $vive }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label for="provincia" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Provincia en histórico</label>
                                            <select name="provincia" id="provincia" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                <option value="">-- Todas --</option>
                                                @foreach($provinciasFiltradas as $provincia)
                                                    <option value="{{ $provincia }}" {{ request('provincia') == $provincia ? 'selected' : '' }}>
                                                        {{ $provincia }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label for="alertas" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Alertas</label>
                                            <select name="alertas" id="alertas" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                <option value="">-- Selecciona --</option>
                                                <option value="si" {{ request('alertas') == 'si' ? 'selected' : '' }}>Sí</option>
                                                <option value="no" {{ request('alertas') == 'no' ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                    
                                        <!-- Filtro CONTRATO ESTUDIOS -->
                                        <div>
                                            <label for="contrato_estudios" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Contrato Estudios</label>
                                            <select name="contrato_estudios" id="contrato_estudios" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                <option value="">-- Selecciona --</option>
                                                <option value="si" {{ request('contrato_estudios') == 'si' ? 'selected' : '' }}>Sí</option>
                                                <option value="no" {{ request('contrato_estudios') == 'no' ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                        
                        
                                        <div>
                                            <label for="fecha_ingreso" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fecha de ingreso (rango)</label>
                                            <input type="text" name="fecha_ingreso" id="fecha_ingreso"
                                                value="{{ request('fecha_ingreso') }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-black"
                                                placeholder="Selecciona un rango de fechas"
                                                readonly 
                                        />
                                        </div>
                        
                                        <!-- Incluye flatpickr desde CDN -->
                                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                                        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                        
                                        <script>
                                            flatpickr("#fecha_ingreso", {
                                                mode: "range",
                                                dateFormat: "Y-m-d"
                                            });
                                        </script>
                                        <div>
                                            <label for="fecha_pase_anterior" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fecha del pase anterior</label>
                                            <input type="text" name="fecha_pase_anterior" id="fecha_pase_anterior"
                                                value="{{ request('fecha_pase_anterior') }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-black"
                                                placeholder="Selecciona un rango de fechas"
                                                readonly />
                                        </div>
                        
                                        <!-- Incluye flatpickr desde CDN -->
                                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                                        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                        
                                        <script>
                                            flatpickr("#fecha_pase_anterior", {
                                                mode: "range",
                                                dateFormat: "Y-m-d"
                                            });
                                        </script>
                        
                                        <div>
                                            <label for="fecha_pase_actual" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fecha del pase actual</label>
                                            <input type="text" name="fecha_pase_actual" id="fecha_pase_actual"
                                                value="{{ request('fecha_pase_actual') }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-black"
                                                placeholder="Selecciona un rango de fechas"
                                                readonly />
                                        </div>
                        
                                        <!-- Incluye flatpickr desde CDN -->
                                            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                                            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                        
                                        <script>
                                            flatpickr("#fecha_pase_actual", {
                                                mode: "range",
                                                dateFormat: "Y-m-d"
                                            });
                                        </script>
                                    </div>
                        
                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">Filtrar</button>
                                        <a href="{{ route('usuarios.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                                            Limpiar búsqueda
                                        </a>
                                         
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            {{-- Importar Excel --}}
                            {{-- Exportar Excel --}}
                            <form method="GET" action="{{ route('usuarios.export', ['type' => 'excel']) }}">
                                @foreach(request()->all() as $key => $value)
                                    @if(is_array($value))
                                        @foreach($value as $subvalue)
                                            <input type="hidden" name="{{ $key }}[]" value="{{ $subvalue }}">
                                        @endforeach
                                    @else
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endif
                                @endforeach
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                    Exportar Excel
                                </button>
                            </form>
                        
                            {{-- Exportar PDF --}}
                            <form method="GET" action="{{ route('usuarios.export', ['type' => 'pdf']) }}">
                                @foreach(request()->all() as $key => $value)
                                    @if(is_array($value))
                                        @foreach($value as $subvalue)
                                            <input type="hidden" name="{{ $key }}[]" value="{{ $subvalue }}">
                                        @endforeach
                                    @else
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endif
                                @endforeach
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                    Exportar PDF
                                </button>
                            </form>
                        </div>
                                          
                        <script>
                            document.addEventListener("DOMContentLoaded", function(event) {
                            document.getElementById('updateProductButton').click();
                            });
                        </script>
                    
                    </div>     
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">

                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Cédula</th>
                                <th scope="col" class="px-4 py-3">Grado</th>
                                <th scope="col" class="px-4 py-3">Nombre</th>
                                <th scope="col" class="px-4 py-3">Sexo</th>
                                <th scope="col" class="px-4 py-3">Tipo de Personal</th>
                                <th scope="col" class="px-4 py-3">Perfil</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($usuarios as $usuario)

                            <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                
                                <th scope="row" class="flex items-center px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    
                                    {{ $usuario->cedula }}
                                </th>
                                <td class="px-4 py-2">
                                    <span class="bg-primary-100 text-primary-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-primary-900 dark:text-primary-300">{{ $usuario->grado }}</span>
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <div class="flex items-center">
                                        <div class="inline-block w-4 h-4 mr-2 bg-green-700 rounded-full"></div>
                                        {{ $usuario->apellidos_nombres }}
                                    </div>
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $usuario->sexo }}</td>
                                
                                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <div class="flex items-center">
                                    {{ $usuario->tipo_personal }}
                                    </div>
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white"><a href="{{ route('usuarios.show', $usuario->id) }}"
                                    class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm shadow">
                                    Ver Perfil
                                </a></td>
                            </tr>  
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="">
                    <nav aria-label="Page navigation example" class="m-4">
                        {{ $usuarios->appends(request()->query())->links() }}
                    </nav>   
                </div>
            </div>
        </div>
    </section>
    
@endsection
