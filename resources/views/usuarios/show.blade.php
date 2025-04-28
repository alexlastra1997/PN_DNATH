@extends('layouts.app') {{-- o tu layout principal --}}

@section('content')
<div class="h-full bg-gray-100 p-4">

    <div class="flex flex-col gap-4">
        <div class="flex flex-col xl:flex-row gap-4">
            {{-- Tarjeta de usuario --}}
            <div class="w-full 2xl:w-1/3 bg-white rounded-lg shadow-md overflow-hidden">
                <div class="flex flex-col items-center p-6">
                    <div class="relative w-full">
                        <img src="https://img.freepik.com/vector-gratis/fondo-geometrico-azul-blanco_1189-293.jpg" class="w-full h-32 object-cover rounded-lg">
                        <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2">
                            <div class="w-24 h-24 rounded-full border-4 border-white bg-pink-400 overflow-hidden">
                                <img src="https://img.freepik.com/vector-gratis/circulo-azul-usuario-blanco_78370-4707.jpg" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                    <div class="mt-16 text-center">
                        <h4 class="text-xl font-bold text-gray-800">{{ $usuario->apellidos_nombres }}</h4>
                        <p class="text-gray-600">CI: {{ $usuario->cedula }}</p>
                    </div>

                    <div class="flex justify-center gap-8 mt-6">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-800">{{ $usuario->grado }}</p>
                            <p class="text-sm text-gray-500">Grado</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-800">{{ $usuario->tipo_personal }}</p>
                            <p class="text-sm text-gray-500">Tipo de Personal</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-800">{{ $usuario->cdg_promocion }}</p>
                            <p class="text-sm text-gray-500">Promoción</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Información del servidor policial --}}
            <div class="w-full 2xl:w-2/3 bg-white rounded-lg shadow-md p-6">
                <h4 class="text-xl font-bold text-gray-900 mb-4">Información del Servidor Policial</h4>
                <ul class="space-y-2 text-gray-700">
                    @php
                        $info = [
                            'Nombre' => $usuario->apellidos_nombres,
                            'Sexo' => $usuario->sexo,
                            'Unidad' => $usuario->unidad,
                            'Función' => $usuario->funcion,
                            'Causa de pase' => $usuario->causa_pase,
                            'Estado Civil' => $usuario->estado_civil,
                            'Promoción' => $usuario->promocion,
                            'Fecha de ingreso' => $usuario->fecha_ingreso,
                        ];
                    @endphp

                    @foreach ($info as $label => $value)
                        <li class="flex flex-col md:flex-row md:items-center border-b py-2">
                            <span class="font-semibold w-40">{{ $label }}:</span>
                            <span>{{ $value }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Tiempo --}}
        @include('usuarios.tiempo')

    
        </div>
    </div>

</div>
@endsection






