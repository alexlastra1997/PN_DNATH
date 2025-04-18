{{-- resources/views/usuarios/index.blade.php --}}
@extends('layouts.app')

@section('content')
@include('usuarios.cards')

<section class="bg-gray-50 dark:  sm:py-5">
  <div class="px-4 mx-auto max-w-screen-2xl lg:px-12">
      <div class="relative overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:rounded-lg">
          <div class="flex flex-col px-4 py-3 space-y-3 lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:space-x-4">
            <form action="#" method="GET" class="hidden md:block md:pl-2">
            <label for="topbar-search" class="sr-only">Search</label>
            <div class="relative md:w-64 md:w-96">
            <div
                class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none"
            >
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
                name="email"
                id="topbar-search"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Search"
            />
            </div>
        </form>

            <!-- Modal toggle -->
            <button data-modal-target="default-modal" data-modal-toggle="default-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                Filtro
            </button>
            
            <!-- Main modal -->
            <div id="default-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-2xl max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Terms of Service
                            </h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        
                        <!-- Modal footer -->
                        <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
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
                                    <a href="{{ route('usuarios.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                                        Limpiar búsqueda
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
                            Ver Detalles
                         </a></td>
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
            




    <nav aria-label="Page navigation example">
        <ul class="inline-flex -space-x-px text-base h-10">
        <li>
            <a href="#" class="flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Previous</a>
        </li>
        <li>
            <a href="#" class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">{{ $usuarios->appends(request()->input())->links() }}</a>
        </li>
        <li>
            <a href="#" class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Next</a>
        </li>
        </ul>
    </nav>        
      </div>
  </div>
</section>

@endsection
