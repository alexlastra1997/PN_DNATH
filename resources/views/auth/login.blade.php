{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.guest')
{{-- resources/views/auth/login.blade.php --}}
@vite(['resources/css/app.css','resources/js/app.js'])

<section class="bg-white dark:bg-gray-900">
    <div class="grid lg:h-screen lg:grid-cols-2">

        {{-- Columna izquierda: CARD con el formulario --}}
        <div class="flex items-center justify-center px-4 py-10 lg:py-0 sm:px-6">
            <div class="w-full max-w-md xl:max-w-xl">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-100 dark:border-gray-700">
                    <form class="p-6 md:p-8 space-y-5"
                          method="POST"
                          action="{{ route('login') }}">
                        @csrf

                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Welcome back</h1>

                        {{-- Accesos sociales (opcionales) --}}
                        <div class="space-y-3">
                            @if (Route::has('social.redirect'))
                                <a href="{{ route('social.redirect','google') }}"
                                   class="w-full inline-flex items-center justify-center py-2.5 px-5 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                    <svg class="w-5 h-5 mr-2" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#g)"><path d="M20.308 10.23c0-.68-.055-1.364-.173-2.032H10.703v3.85h5.402c-.224 1.242-.945 2.34-1.999 3.039v2.498h3.222c1.893-1.742 2.98-4.314 2.98-7.357z" fill="#3F83F8"/><path d="M10.702 20c2.697 0 4.971-.886 6.628-2.414l-3.223-2.499c-.897.611-2.054.956-3.402.956-2.608 0-4.82-1.76-5.614-4.126H1.766v2.576C3.463 17.87 6.92 20 10.702 20z" fill="#34A853"/><path d="M5.089 11.917a6.23 6.23 0 010-3.83V5.512H1.767a9.96 9.96 0 000 8.98l3.322-2.575z" fill="#FBBC04"/><path d="M10.702 3.958c1.426-.022 2.804.515 3.836 1.5l2.855-2.855C15.586.905 13.186-.029 10.702.001 6.92.002 3.463 2.133 1.766 5.513l3.322 2.576C5.878 5.718 8.093 3.958 10.702 3.958z" fill="#EA4335"/></g>
                                        <defs><clipPath id="g"><rect width="20" height="20" fill="#fff" transform="translate(.5)"/></clipPath></defs>
                                    </svg>
                                    Sign in with Google
                                </a>
                            @endif
                        </div>

                        {{-- Separador --}}
                        <div class="flex items-center">
                            <div class="w-full h-px bg-gray-200 dark:bg-gray-700"></div>
                            <div class="px-3 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">or</div>
                            <div class="w-full h-px bg-gray-200 dark:bg-gray-700"></div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="block w-full p-2.5 rounded-lg border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500
                            dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                   placeholder="Enter your email" required autofocus>
                            @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Password</label>
                            <input type="password" name="password" id="password" placeholder="••••••••"
                                   class="block w-full p-2.5 rounded-lg border border-gray-300 bg-gray-50 text-gray-900 focus:ring-blue-500 focus:border-blue-500
                            dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                   required>
                            @error('password') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Remember + Forgot --}}
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-300">
                                <input id="remember" name="remember" type="checkbox"
                                       class="w-4 h-4 rounded border-gray-300 bg-gray-50 focus:ring-2 focus:ring-blue-300
                              dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600">
                                Remember me
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        {{-- Estado opcional --}}
                        @if (session('status'))
                            <p class="text-sm text-green-600">{{ session('status') }}</p>
                        @endif

                        {{-- Submit --}}
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300
                           font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Sign in to your account
                        </button>

                        {{-- Registro --}}
                        @if (Route::has('register'))
                            <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                                Don't have an account?
                                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:underline dark:text-blue-400">Sign up</a>
                            </p>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        {{-- Columna derecha: Panel de marca (altura completa, mejor contraste) --}}
        <div class="flex items-center justify-center px-4 py-10 lg:py-0 sm:px-6 bg-gradient-to-br from-blue-600 to-blue-700">
            <div class="max-w-md xl:max-w-xl">
                <a href="#" class="flex items-center mb-5 text-2xl font-semibold text-white">
                    <img class="w-8 h-8 mr-2" src="{{ asset('images/dnath.png') }}" alt="logo">
                    DNATH
                </a>

                <h2 class="mb-4 text-3xl xl:text-5xl font-extrabold leading-tight text-white">
                    Explora el visualizador del orgánico institucional.
                </h2>

                <p class="mb-8 font-light text-blue-100">
                    Centraliza traslados, vacantes y ocupación de cargos con filtros en tiempo real, exportaciones y paneles.
                </p>

                <div class="flex items-center divide-x divide-blue-400">
                    <div class="flex pr-3 -space-x-4 sm:pr-5">
                        <img class="w-10 h-10 border-2 border-white rounded-full" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/bonnie-green.png" alt="">
                        <img class="w-10 h-10 border-2 border-white rounded-full" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/jese-leos.png" alt="">
                        <img class="w-10 h-10 border-2 border-white rounded-full" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/roberta-casas.png" alt="">
                        <img class="w-10 h-10 border-2 border-white rounded-full" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/thomas-lean.png" alt="">
                    </div>
                    <span class="pl-3 text-sm text-blue-100 sm:pl-5">
            Over <span class="font-medium text-white">15.7k</span> Happy Users
          </span>
                </div>
            </div>
        </div>

    </div>
</section>
