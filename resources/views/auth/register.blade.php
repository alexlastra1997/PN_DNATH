{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.guest')

@section('content')
    <section class="bg-white dark:bg-gray-900">
        <div class="grid lg:h-screen lg:grid-cols-2">

            {{-- Columna Izquierda: Formulario --}}
            <div class="flex justify-center items-center py-6 px-4 lg:py-0 sm:px-0">
                <form method="POST" action="{{ route('register') }}"
                      class="space-y-4 max-w-md md:space-y-6 xl:max-w-xl w-full bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    @csrf

                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Crea tu cuenta</h2>

                    {{-- Errores --}}
                    @if ($errors->any())
                        <div class="text-red-500 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Nombre --}}
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Nombre completo</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Correo electrónico</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Contraseña</label>
                        <input type="password" name="password" id="password" required
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>

                    {{-- Confirmar Password --}}
                    <div>
                        <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                                focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>

                    {{-- Terminos --}}
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" required
                                   class="w-4 h-4 bg-gray-50 border border-gray-300 rounded
                                    focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-light text-gray-500 dark:text-gray-300">
                                Al registrarte, aceptas nuestros
                                <a href="#" class="font-medium text-blue-600 hover:underline dark:text-blue-400">Términos de uso</a>
                                y
                                <a href="#" class="font-medium text-blue-600 hover:underline dark:text-blue-400">Política de privacidad</a>.
                            </label>
                        </div>
                    </div>

                    {{-- Botón --}}
                    <button type="submit"
                            class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none
                             focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center
                             dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Crear cuenta
                    </button>

                    {{-- Link a Login --}}
                    <p class="text-sm font-light text-gray-500 dark:text-gray-300">
                        ¿Ya tienes una cuenta?
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:underline dark:text-blue-400">Inicia sesión</a>
                    </p>
                </form>
            </div>

            {{-- Columna Derecha: Panel de marca --}}
            <div class="flex justify-center items-center py-6 px-4 bg-blue-600 lg:py-0 sm:px-0">
                <div class="max-w-md xl:max-w-xl">
                    <a href="#" class="flex items-center mb-4 text-2xl font-semibold text-white">
                        <img class="w-8 h-8 mr-2" src="{{ asset('images/dnath.png') }}" alt="logo">
                        DNATH
                    </a>
                    <h1 class="mb-4 text-3xl font-extrabold tracking-tight leading-none text-white xl:text-5xl">Explora el orgánico institucional</h1>
                    <p class="mb-4 font-light text-blue-100 lg:mb-8">Centraliza registros, traslados y vacantes con paneles dinámicos y filtros en tiempo real.</p>
                    <div class="flex items-center divide-x divide-blue-400">
                        <div class="flex pr-3 -space-x-4 sm:pr-5">
                            <img class="w-10 h-10 border-2 border-white rounded-full" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png" alt="">
                            <img class="w-10 h-10 border-2 border-white rounded-full" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/jese-leos.png" alt="">
                            <img class="w-10 h-10 border-2 border-white rounded-full" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/roberta-casas.png" alt="">
                            <img class="w-10 h-10 border-2 border-white rounded-full" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/thomas-lean.png" alt="">
                        </div>
                        <span class="pl-3 text-sm text-blue-100 sm:pl-5">
                      Más de <span class="font-medium text-white">15.7k</span> usuarios satisfechos
                  </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
