{{-- resources/views/usuarios/index.blade.php --}}
@extends('layouts.app')

@section('content')

<section class="bg-gray-50 dark:  sm:py-5">
  <div class="px-4 mx-auto max-w-screen-2xl lg:px-12">
      <div class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
          <div class="flex flex-col px-4 py-3 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
              <div class="flex items-center flex-1 space-x-4">
                  <h5>
                      <span class="text-gray-500">All Products:</span>
                      <span class="dark:text-white">123456</span>
                  </h5>
                  <h5>
                      <span class="text-gray-500">Total sales:</span>
                      <span class="dark:text-white">$881.4k</span>
                  </h5>
              </div>
              <div class="flex flex-col flex-shrink-0 space-y-3 md:flex-row md:items-center lg:justify-end md:space-y-0 md:space-x-3">
              <form method="GET" action="{{ route('usuarios.index') }}" class="mb-6 bg-white p-4 rounded shadow">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-medium">Buscar por Cédula</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="mt-1 w-full rounded border-gray-300 shadow-sm" placeholder="Ingrese cédula">
                    </div>

                    <div>
                        <label class="block font-medium">Sexo</label>
                        <div class="flex items-center space-x-4 mt-1">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="sexo[]" value="H" {{ in_array('H', request('sexo', [])) ? 'checked' : '' }} class="form-checkbox">
                                <span class="ml-2">Hombre</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="sexo[]" value="M" {{ in_array('M', request('sexo', [])) ? 'checked' : '' }} class="form-checkbox">
                                <span class="ml-2">Mujer</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium">Número de Hijos</label>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @for ($i = 0; $i <= 5; $i++)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="hijos[]" value="{{ $i }}" {{ in_array((string)$i, request('hijos', [])) ? 'checked' : '' }} class="form-checkbox">
                                    <span class="ml-1">{{ $i }}</span>
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">Filtrar</button>

                    <a href="{{ route('usuarios.exportExcel', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">Exportar Excel</a>
                    <a href="{{ route('usuarios.exportPDF', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow">Exportar PDF</a>

                </div>
            </form>
            <form action="{{ route('usuarios.exportExcel') }}" method="GET">
                <button type="submit" class="btn btn-success">Exportar Excel</button>
            </form>
              </div>
          </div>
          <div class="overflow-x-auto">
              <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">

                <!-- Formulario de filtro -->
                    
                <form method="GET" action="{{ route('usuarios.index') }}" class="mb-6 flex flex-wrap gap-4 items-center">

                    <!-- Campo de búsqueda por cédula -->
                    <input 
                        type="text" 
                        name="cedula" 
                        placeholder="Buscar por cédula" 
                        value="{{ request('cedula') }}"
                        class="px-4 py-2 border rounded-lg w-64 focus:outline-none focus:ring focus:border-blue-300"
                    />

                    <!-- Botón de buscar -->
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Buscar
                    </button>

                    <!-- Botón de limpiar -->
                    <a href="{{ route('usuarios.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                        Limpiar búsqueda
                    </a>
                </form>

                <form action="{{ route('usuarios.exportPDF') }}" method="GET" class="inline">
                    <button type="submit" class="btn btn-danger">Exportar PDF</button>
                </form>

                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        
                        <th scope="col" class="px-4 py-3">Cédula</th>
                        <th scope="col" class="px-4 py-3">Grado</th>
                        <th scope="col" class="px-4 py-3">Nombre</th>
                        <th scope="col" class="px-4 py-3">Sexo</th>
                        <th scope="col" class="px-4 py-3">Unidad</th>
                        <th scope="col" class="px-4 py-3">Función</th>
                        <th scope="col" class="px-4 py-3">Tipo de Personal</th>
                        <th scope="col" class="px-4 py-3">Acción</th>

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
                                <div class="inline-block w-4 h-4 mr-2 bg-red-700 rounded-full"></div>
                                {{ $usuario->apellidos_nombres }}
                            </div>
                        </td>
                        <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $usuario->sexo }}</td>
                        <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $usuario->unidad }}</td>
                        <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <div class="flex items-center">
                            {{ $usuario->funcion }}
                            </div>
                        </td>
                        <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <div class="flex items-center">
                            {{ $usuario->tipo_personal }}
                            </div>
                        </td>
                        <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">Just now</td>
                    </tr>  
                @endforeach
                  </tbody>
              </table>
          </div>
          <nav class="flex flex-col items-start justify-between p-4 space-y-3 md:flex-row md:items-center md:space-y-0" aria-label="Table navigation">
              <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                  Showing
                  <span class="font-semibold text-gray-900 dark:text-white">1-10</span>
                  of
                  <span class="font-semibold text-gray-900 dark:text-white">1000</span>
              </span>
              <nav class="flex flex-warp items-start justify-end p-4 space-y-3 md:flex-row md:items-center md:space-y-0" aria-label="Table navigation">
                {{ $usuarios->appends(request()->input())->links() }}
                </nav>
          </nav>
      </div>
  </div>
</section>

@endsection
