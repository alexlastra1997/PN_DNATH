@extends('layouts.app')

@section('content')

<section class="bg-white ">
    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-8 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <h1 class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl ">PERESTROIKA - SISTEMA INTEGRAL DE LA DNATH</h1>
            
        </div>
        <div class="hidden lg:mt-0 lg:col-span-5 lg:flex">
            <img src="{{ asset('images/DNATH2.jpeg') }}"alt="Placeholder Image" class="object-fill w-full h-full">
        </div>                
    </div>
</section>

    <div>  
        <div class="container m-auto px-6 text-gray-500 md:px-12 xl:px-0">
             <div class="bg-white rounded-2xl shadow-xl px-8 py-12 sm:px-12 lg:px-8 mt-8">
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-4 mb-12">
                    <article>
                        <section class="mt-6 grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3 gap-x-6 gap-y-8">
                            <article class="relative w-full h-64 bg-cover bg-center group rounded-lg overflow-hidden shadow-lg hover:shadow-2xl  transition duration-300 ease-in-out"
                                style="background-image: url('https://images.unsplash.com/photo-1623479322729-28b25c16b011?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1740&q=80');">
                                <div class="absolute inset-0 bg-black bg-opacity-50 group-hover:opacity-75 transition duration-300 ease-in-out"></div>
                                <div class="relative w-full h-full px-4 sm:px-6 lg:px-4 flex justify-center items-center">
                                    <h3 class="text-center">
                                        <a class="text-white text-2xl font-bold text-center"  href="{{ route('usuarios.index') }}">
                                            <span class="absolute inset-0"></span>
                                            ISKAT
                                        </a>
                                    </h3>
                                </div>
                            </article>
                            <article class="relative w-full h-64 bg-cover bg-center group rounded-lg overflow-hidden shadow-lg hover:shadow-2xl  transition duration-300 ease-in-out"
                                style="background-image: url('https://images.unsplash.com/photo-1569012871812-f38ee64cd54c?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80');">
                                <div class="absolute inset-0 bg-black bg-opacity-50 group-hover:opacity-75 transition duration-300 ease-in-out"></div>
                                <div class="relative w-full h-full px-4 sm:px-6 lg:px-4 flex justify-center items-center">
                                    <h3 class="text-center">
                                        <a class="text-white text-2xl font-bold text-center" href="{{ route('cargos.cards') }}">
                                            <span class="absolute inset-0"></span>
                                            KADRY 
                                        </a>
                                    </h3>
                                </div>
                            </article>
                            <article class="relative w-full h-64 bg-cover bg-center group rounded-lg overflow-hidden shadow-lg hover:shadow-2xl  transition duration-300 ease-in-out"
                                style="background-image: url('https://images.unsplash.com/photo-1511376777868-611b54f68947?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1170&q=80');">
                                <div class="absolute inset-0 bg-black bg-opacity-50 group-hover:opacity-75 transition duration-300 ease-in-out"></div>
                                <div class="relative w-full h-full px-4 sm:px-6 lg:px-4 flex justify-center items-center">
                                    <h3 class="text-center">
                                        <a class="text-white text-2xl font-bold text-center" href="{{ route('provincias.index') }}">
                                            <span class="absolute inset-0"></span>
                                            KARTA
                                        </a>
                                    </h3>
                                </div>
                            </article>
                            <article class="relative w-full h-64 bg-cover bg-center group rounded-lg overflow-hidden shadow-lg hover:shadow-2xl  transition duration-300 ease-in-out"
                                style="background-image: url('https://www.freepik.es/foto-gratis/mano-usando-computadora-portatil-pantalla-virtual-documento-aprobar-linea-concepto-gestion-erp-garantia-calidad-papel_24755711.htm#fromView=search&page=1&position=3&uuid=963ad3c0-b0dd-4cda-bf47-c5296fb16e6d&query=DATA');">
                                <div class="absolute inset-0 bg-black bg-opacity-50 group-hover:opacity-75 transition duration-300 ease-in-out"></div>
                                <div class="relative w-full h-full px-4 sm:px-6 lg:px-4 flex justify-center items-center">
                                    <h3 class="text-center">
                                        <a class="text-white text-2xl font-bold text-center"  href="{{ url('/organico-efectivo') }}" >
                                            <span class="absolute inset-0"></span>
                                            O. EFECTIVO
                                        </a>
                                    </h3>
                                </div>
                            </article>
                            <article class="relative w-full h-64 bg-cover bg-center group rounded-lg overflow-hidden shadow-lg hover:shadow-2xl  transition duration-300 ease-in-out"
                                style="background-image: url('https://img.freepik.com/foto-gratis/primer-plano-manos-usando-computadora-portatil-pantalla-que-muestra-datos-analisis_53876-23014.jpg?t=st=1749048512~exp=1749052112~hmac=a53784aac069a5b6b26e8f7832e3582de66d1ade1f06c2bc71b8d93c9f2e998c&w=2000');">
                                <div class="absolute inset-0 bg-black bg-opacity-50 group-hover:opacity-75 transition duration-300 ease-in-out"></div>
                                <div class="relative w-full h-full px-4 sm:px-6 lg:px-4 flex justify-center items-center">
                                    <h3 class="text-center">
                                        <a class="text-white text-2xl font-bold text-center"  href="{{ url('/nomenclatura') }}" >
                                            <span class="absolute inset-0"></span>
                                            NOMENCLATURAS
                                        </a>
                                    </h3>
                                </div>
                            </article>
                        </section>
                    </article>
                </section>
            </div>
        </div>
    </div>
@endsection