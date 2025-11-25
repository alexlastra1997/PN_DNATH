{{-- resources/views/panel_exterior.blade.php --}}
@extends('layouts.app')

@section('content')
    <section class="min-h-screen bg-gray-100 py-8 lg:py-12">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            {{-- Título principal --}}
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900 mb-8">
                MATRIZ DE ANÁLISIS DE VACANTES DE COMANDANTES-SUBCOMANDANTES-JEFES-SUBJEFES
            </h1>

            <div class="grid grid-cols-1 gap-6 md:gap-8 md:grid-cols-2 lg:grid-cols-3">

                {{-- CARD 1: Mapa NDESC --}}
                <a href="{{ route('mapa.ndesc') }}"
                   class="group relative block rounded-xl bg-white shadow-lg hover:shadow-2xl duration-200 ease-out">
                    {{-- Barra lateral de color --}}
                    <div class="absolute inset-y-0 left-0 w-2 bg-blue-400 group-hover:bg-blue-600 transition-colors duration-200"></div>

                    <div class="py-6 pr-6 pl-9 lg:p-8 lg:pl-10">
                        <div class="flex items-center">
                            <p class="text-sm font-bold uppercase text-gray-500 group-hover:text-gray-700">Mapa NDESC</p>
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4 text-gray-500 group-hover:text-gray-700"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>

                        <h2 class="mt-3 text-2xl font-semibold text-gray-800 group-hover:text-gray-900">
                            Comparativo Orgánico vs Efectivo
                        </h2>
                        <p class="mt-2 text-sm font-medium text-gray-600 group-hover:text-gray-700 leading-6">
                            Vista por niveles desconcentrados, con estados por cargo.
                        </p>

                        {{-- Imagen debajo del texto (16:9 uniforme) --}}
                        <figure class="mt-6 aspect-[16/9] w-full overflow-hidden rounded-lg">
                            <img
                                src="https://imagenes.primicias.ec/files/image_480_270/uploads/2024/05/26/665383e57544a.jpeg"
                                alt="Mapa NDESC"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                            />
                        </figure>
                    </div>
                </a>

                {{-- CARD 2: Mapa DIREC --}}
                <a href="{{ route('mapa_direc.index') }}"
                   class="group relative block rounded-xl bg-white shadow-lg hover:shadow-2xl duration-200 ease-out">
                    <div class="absolute inset-y-0 left-0 w-2 bg-yellow-500 group-hover:bg-amber-600 transition-colors duration-200"></div>

                    <div class="py-6 pr-6 pl-9 lg:p-8 lg:pl-10">
                        <div class="flex items-center">
                            <p class="text-sm font-bold uppercase text-gray-500 group-hover:text-gray-700">Mapa DIREC</p>
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4 text-gray-500 group-hover:text-gray-700"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>

                        <h2 class="mt-3 text-2xl font-semibold text-gray-800 group-hover:text-gray-900">
                            Vista por Direcciones
                        </h2>
                        <p class="mt-2 text-sm font-medium text-gray-600 group-hover:text-gray-700 leading-6">
                            Orgánico vs efectivo por dirección, con estados por cargo.
                        </p>

                        <figure class="mt-6 aspect-[16/9] w-full overflow-hidden rounded-lg">
                            <img
                                src="https://policiajudicial.gob.ec/wp-content/uploads/2025/03/6-1024x624.jpg"
                                alt="Mapa DIREC"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                            />
                        </figure>
                    </div>
                </a>

                {{-- CARD 3: Agregados Policiales --}}
                <a href="{{ route('agregados.policiales') }}"
                   class="group relative block rounded-xl bg-white shadow-lg hover:shadow-2xl duration-200 ease-out">
                    <div class="absolute inset-y-0 left-0 w-2 bg-green-400 group-hover:bg-green-600 transition-colors duration-200"></div>

                    <div class="py-6 pr-6 pl-9 lg:p-8 lg:pl-10">
                        <div class="flex items-center">
                            <p class="text-sm font-bold uppercase text-gray-500 group-hover:text-gray-700">Agregados Policiales</p>
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4 text-gray-500 group-hover:text-gray-700"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>

                        <h2 class="mt-3 text-2xl font-semibold text-gray-800 group-hover:text-gray-900">
                            Cargos en el Exterior
                        </h2>
                        <p class="mt-2 text-sm font-medium text-gray-600 group-hover:text-gray-700 leading-6">
                            Dos tablas por país: cargos (estado) y ocupantes (usuarios).
                        </p>

                        <figure class="mt-6 aspect-[16/9] w-full overflow-hidden rounded-lg">
                            <img
                                src="https://thumbs.dreamstime.com/b/polic%C3%ADa-de-combate-en-miniatura-parado-un-mapa-del-mundo-polic%C3%ADas-parados-el-mundial-concepto-seguridad-nacional-198198988.jpg"
                                alt="Agregados Policiales"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                            />
                        </figure>
                    </div>
                </a>

            </div>
        </div>
    </section>
@endsection
